<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentModeration
{
    /**
     * Returns an array: ["isHateSpeech" => bool, "reasons" => string[], "severity" => int, "score" => float]
     */
    public function checkForHateSpeech(string $text): array
    {
        $apiKey = config('services.gemini.key');

        // First, perform strict local Indonesian profanity detection
        $localCheck = $this->performStrictLocalCheck($text);
        if ($localCheck['isHateSpeech']) {
            Log::warning('Content blocked by strict local check', [
                'text' => substr($text, 0, 100) . '...',
                'reasons' => $localCheck['reasons'],
                'severity' => $localCheck['severity'],
                'score' => $localCheck['score']
            ]);
            return $localCheck;
        }

        if (empty($apiKey)) {
            // If no API key, rely on local detection only
            return [
                'isHateSpeech' => false,
                'reasons' => [],
                'severity' => 0,
                'score' => 0.0,
            ];
        }

        try {
            // Collect advisory matches from local CSV
            $abusiveMatches = $this->matchAbusiveTerms($text);

            // Call Gemini for nuanced detection (primary decision source)
            $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

$instruction = <<<EOT
Anda adalah detektor hate speech khusus untuk konten bahasa Indonesia. Tentukan apakah TEKS mengandung hate speech, umpatan kasar, atau konten yang tidak pantas.

Hate speech termasuk konten yang:
- Menyerang, mendehumanisasi, atau menyerukan kekerasan terhadap kelompok tertentu
- Menggunakan umpatan kasar bahasa Indonesia (seperti: anjing, babi, keparat, goblok, dll)
- Menggunakan kata-kata vulgar atau seksual yang kasar
- Menyerang agama, suku, gender, orientasi seksual, atau disabilitas
- Menggunakan kata-kata yang merendahkan atau menghina

PENTING: Fokus pada deteksi umpatan bahasa Indonesia dan konten yang tidak pantas. Lebih baik memblokir konten yang meragukan daripada membiarkan konten yang tidak pantas.

Output HANYA JSON yang valid, tanpa teks tambahan:
{"decision":"ALLOW"|"BLOCK","labels":[string], "reason":string, "severity":1-5}

Jika ragu, pilih "BLOCK" untuk keamanan.
TEKS:
<<<
{$text}
>>>
EOT;

            $requestBody = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [ ['text' => $instruction] ],
                    ],
                ],
                // Disable blocking so the model can analyze unsafe text
                'safetySettings' => [
                    [ 'category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE' ],
                    [ 'category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE' ],
                    [ 'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE' ],
                    [ 'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE' ],
                ],
            ];

            $response = Http::timeout(15)
                ->withQueryParameters(['key' => $apiKey])
                ->post($endpoint, $requestBody);

            if (!$response->successful()) {
                Log::warning('Gemini moderation API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                // On API failure, fall back to local detection
                return $this->performStrictLocalCheck($text);
            }

            $data = $response->json();
            Log::info('Gemini moderation response', [ 
                'text_preview' => substr($text, 0, 50) . '...',
                'response' => $data 
            ]);

            // Extract the model text output
            $modelText = '';
            if (!empty($data['candidates'][0]['content']['parts'])) {
                $parts = $data['candidates'][0]['content']['parts'];
                foreach ($parts as $part) {
                    if (isset($part['text'])) {
                        $modelText .= $part['text'];
                    }
                }
            }

            $modelText = trim((string) $modelText);
            if ($modelText === '') {
                // If we got nothing, fall back to local detection
                return $this->performStrictLocalCheck($text);
            }

            // Try to parse JSON from the text
            if (preg_match('/\{[\s\S]*\}/', $modelText, $m)) {
                $jsonText = $m[0];
            } else {
                $jsonText = $modelText;
            }

            try {
                $parsed = json_decode($jsonText, true, 512, JSON_THROW_ON_ERROR);
                $decision = strtoupper((string) ($parsed['decision'] ?? 'ALLOW'));
                $labels = isset($parsed['labels']) && is_array($parsed['labels']) ? $parsed['labels'] : [];
                $reason = isset($parsed['reason']) && is_string($parsed['reason']) ? $parsed['reason'] : '';
                $severity = isset($parsed['severity']) ? (int) $parsed['severity'] : 1;

                if ($decision === 'BLOCK') {
                    // Merge Gemini labels with advisory CSV matches (unique)
                    $reasons = !empty($labels) ? $labels : ($reason ? [$reason] : ['Hate speech detected']);
                    if (!empty($abusiveMatches)) {
                        foreach ($abusiveMatches as $term) {
                            if (!in_array($term, $reasons, true)) {
                                $reasons[] = $term;
                            }
                        }
                    }
                    
                    // Calculate severity score
                    $score = $this->calculateSeverityScore($text, $reasons, $severity);
                    
                    Log::warning('Content blocked by Gemini API', [
                        'text' => substr($text, 0, 100) . '...',
                        'reasons' => $reasons,
                        'severity' => $severity,
                        'score' => $score
                    ]);
                    
                    return [
                        'isHateSpeech' => true,
                        'reasons' => $reasons,
                        'severity' => $severity,
                        'score' => $score,
                    ];
                }

                return [ 
                    'isHateSpeech' => false, 
                    'reasons' => [],
                    'severity' => 0,
                    'score' => 0.0,
                ];
            } catch (\Throwable $e) {
                Log::warning('Failed to parse moderation JSON', [ 
                    'output' => $modelText, 
                    'error' => $e->getMessage() 
                ]);
                // On parse error, fall back to local detection for safety
                return $this->performStrictLocalCheck($text);
            }
        } catch (\Throwable $e) {
            Log::error('Content moderation error', [
                'error' => $e->getMessage(),
                'text' => substr($text, 0, 100) . '...'
            ]);
            // On any exception, fall back to local detection
            return $this->performStrictLocalCheck($text);
        }
    }

    /**
     * Performs strict local check for Indonesian profanity and inappropriate content
     */
    private function performStrictLocalCheck(string $text): array
    {
        $abusiveMatches = $this->matchAbusiveTerms($text);
        $patternMatches = $this->detectProfanityPatterns($text);
        $variationMatches = $this->detectProfanityVariations($text);
        
        $allMatches = array_merge($abusiveMatches, $patternMatches, $variationMatches);
        
        if (empty($allMatches)) {
            return [
                'isHateSpeech' => false,
                'reasons' => [],
                'severity' => 0,
                'score' => 0.0,
            ];
        }
        
        // Calculate severity based on matches
        $severity = $this->calculateSeverity($allMatches, $text);
        $score = $this->calculateSeverityScore($text, $allMatches, $severity);
        
        return [
            'isHateSpeech' => true,
            'reasons' => array_unique($allMatches),
            'severity' => $severity,
            'score' => $score,
        ];
    }

    /**
     * Detects profanity patterns and variations
     */
    private function detectProfanityPatterns(string $text): array
    {
        $patterns = [
            // Common Indonesian profanity patterns
            '/\b(anjing|babi|keparat|goblok|bego|bodoh|tolol|dungu|edan|gila)\b/i' => 'Umpatan kasar',
            '/\b(kontol|memek|titit|pantat|bokong|silit)\b/i' => 'Kata vulgar',
            '/\b(ngentot|ngewe|sange|porno|seks)\b/i' => 'Konten seksual',
            '/\b(banci|bencong|homo|lesbi|gay|lgbt)\b/i' => 'Diskriminasi orientasi seksual',
            '/\b(kafir|setan|iblis|terkutuk)\b/i' => 'Penghinaan agama',
            '/\b(cacat|autis|bisu|budek|buta)\b/i' => 'Diskriminasi disabilitas',
            '/\b(cebong|kampret|kampungan|udik)\b/i' => 'Penghinaan sosial',
            '/\b(anjir|anjrit|anjay|anjg)\b/i' => 'Variasi umpatan',
            '/\b(gblk|gblok|goblok|goblog)\b/i' => 'Variasi umpatan',
            '/\b(bego|bego|bejo|bejo)\b/i' => 'Variasi umpatan',
            '/\b(tolol|tolol|tolol|tolol)\b/i' => 'Variasi umpatan',
        ];
        
        $matches = [];
        $textLower = mb_strtolower($text);
        
        foreach ($patterns as $pattern => $category) {
            if (preg_match($pattern, $textLower)) {
                $matches[] = $category;
            }
        }
        
        return $matches;
    }

    /**
     * Detects profanity variations and misspellings
     */
    private function detectProfanityVariations(string $text): array
    {
        $variations = [
            // Common misspellings and variations
            'anjing' => ['anjg', 'anjir', 'anjrit', 'anjay', 'anjeng', 'anjink'],
            'babi' => ['babi', 'b4bi', 'b4b1'],
            'goblok' => ['gblk', 'gblok', 'goblog', 'goblock'],
            'bego' => ['bejo', 'bejo', 'b3go', 'b3j0'],
            'tolol' => ['tolol', 't0l0l', 'tolol'],
            'kontol' => ['kontol', 'k0nt0l', 'kontol'],
            'memek' => ['memek', 'm3m3k', 'memek'],
            'keparat' => ['keparat', 'k3parat', 'keparat'],
            'bangsat' => ['bangsat', 'b4ngs4t', 'bangsat'],
            'kampret' => ['kampret', 'k4mpr3t', 'kampret'],
        ];
        
        $matches = [];
        $textLower = mb_strtolower($text);
        
        foreach ($variations as $base => $vars) {
            foreach ($vars as $var) {
                if (str_contains($textLower, $var)) {
                    $matches[] = "Variasi dari: {$base}";
                    break; // Only count once per base word
                }
            }
        }
        
        return $matches;
    }

    /**
     * Calculates severity level based on matches
     */
    private function calculateSeverity(array $matches, string $text): int
    {
        $severity = 1;
        
        // Count matches
        $matchCount = count($matches);
        
        // Check for multiple profanities
        if ($matchCount >= 3) {
            $severity = 5;
        } elseif ($matchCount >= 2) {
            $severity = 4;
        } elseif ($matchCount >= 1) {
            $severity = 3;
        }
        
        // Check for severe profanities
        $severeTerms = ['kontol', 'memek', 'ngentot', 'ngewe', 'porno', 'seks'];
        $textLower = mb_strtolower($text);
        foreach ($severeTerms as $term) {
            if (str_contains($textLower, $term)) {
                $severity = max($severity, 5);
                break;
            }
        }
        
        // Check for religious slurs
        $religiousTerms = ['kafir', 'setan', 'iblis', 'terkutuk'];
        foreach ($religiousTerms as $term) {
            if (str_contains($textLower, $term)) {
                $severity = max($severity, 4);
                break;
            }
        }
        
        return $severity;
    }

    /**
     * Calculates severity score (0.0 - 1.0)
     */
    private function calculateSeverityScore(string $text, array $reasons, int $severity): float
    {
        $baseScore = $severity / 5.0; // Convert severity to 0.0-1.0 scale
        
        // Adjust score based on text length and match density
        $textLength = mb_strlen($text);
        $matchCount = count($reasons);
        $density = $textLength > 0 ? $matchCount / $textLength : 0;
        
        // Increase score for high density
        if ($density > 0.1) {
            $baseScore = min(1.0, $baseScore + 0.2);
        } elseif ($density > 0.05) {
            $baseScore = min(1.0, $baseScore + 0.1);
        }
        
        return round($baseScore, 2);
    }

    /**
     * Loads abusive terms from CSV and returns any that are found in the given text.
     * Enhanced with better pattern matching for Indonesian profanity.
     */
    private function matchAbusiveTerms(string $text): array
    {
        static $terms = null;
        if ($terms === null) {
            $terms = [];
            $csvPath = resource_path('data/abusive.csv');
            if (is_readable($csvPath)) {
                $lines = @file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#')) {
                        continue;
                    }
                    // Split on first comma to get the term; allow additional columns
                    $parts = explode(',', $line, 2);
                    $term = trim($parts[0]);
                    if ($term !== '') {
                        $terms[] = mb_strtolower($term);
                    }
                }
            }
        }

        if (empty($terms)) {
            return [];
        }

        $textLower = mb_strtolower($text);
        $found = [];
        
        // Use word boundary matching for more accurate detection
        foreach ($terms as $term) {
            if ($term !== '') {
                // Check for exact word matches
                if (preg_match('/\b' . preg_quote($term, '/') . '\b/i', $textLower)) {
                    $found[$term] = $term;
                }
                // Also check for substring matches for compound words
                elseif (str_contains($textLower, $term)) {
                    $found[$term] = $term;
                }
            }
        }
        
        return $found;
    }
}


