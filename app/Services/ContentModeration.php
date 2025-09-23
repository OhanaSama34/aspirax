<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentModeration
{
    /**
     * Returns an array: ["isHateSpeech" => bool, "reasons" => string[]]
     */
    public function checkForHateSpeech(string $text): array
    {
        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            // Fail-safe: if no key, allow content
            return [
                'isHateSpeech' => false,
                'reasons' => [],
            ];
        }

        try {
            // Collect advisory matches from local CSV (do not block solely on this)
            $abusiveMatches = $this->matchAbusiveTerms($text);

            // Call Gemini for nuanced detection (primary decision source)
            // Follow the reference approach: prompt the generative model to return a strict JSON decision
            $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

$instruction = <<<EOT
You are a hate speech detector. Determine if the TEXT contains hate speech.
Hate speech includes content that attacks, dehumanizes, or calls for exclusion/violence against protected classes
(e.g., race, nationality, religion, caste, gender, gender identity, sexual orientation, disability, serious disease, immigration status, age).

Important: Mentions or criticism of ethnicity alone should not be considered hate speech unless there is explicit dehumanization, slurs, or calls for violence/exclusion.

Output ONLY valid minified JSON, no extra text:
{"decision":"ALLOW"|"BLOCK","labels":[string], "reason":string}

If uncertain, choose "ALLOW".
TEXT:
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
                // On API failure, do not block posting
                return [
                    'isHateSpeech' => false,
                    'reasons' => [],
                ];
            }

            $data = $response->json();
            Log::info('Gemini moderation (generateContent) response', [ 'data' => $data ]);

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
                // If we got nothing, do not block
                return [ 'isHateSpeech' => false, 'reasons' => [] ];
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
                    return [
                        'isHateSpeech' => true,
                        'reasons' => $reasons,
                    ];
                }

                return [ 'isHateSpeech' => false, 'reasons' => [] ];
            } catch (\Throwable $e) {
                Log::warning('Failed to parse moderation JSON', [ 'output' => $modelText, 'error' => $e->getMessage() ]);
                // Safe default similar to reference: treat parse error as BLOCK or ALLOW?
                // Keep backend lenient to avoid false positives due to formatting; choose ALLOW here.
                return [ 'isHateSpeech' => false, 'reasons' => [] ];
            }
        } catch (\Throwable $e) {
            // On any exception, allow content but do not fail request path.
            return [
                'isHateSpeech' => false,
                'reasons' => [],
            ];
        }
    }

    /**
     * Loads abusive terms from CSV and returns any that are found in the given text.
     * A simple case-insensitive substring check is used for robustness.
     * CSV format: one term per line, optionally with category/notes separated by comma.
     * Returns array of matched terms.
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
        foreach ($terms as $term) {
            if ($term !== '' && str_contains($textLower, $term)) {
                $found[$term] = $term;
            }
        }
        return $found;
    }
}


