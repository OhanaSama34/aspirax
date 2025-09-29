<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Services\ContentModeration;

class AspiroBot extends Controller
{
     public function index()
    {
        return view('aspiro');
    }

	/**
	 * Handle chat messages to discuss current political issues.
	 * Uses ContentModeration to screen user input and Gemini for responses.
	 */
	public function chat(Request $request, ContentModeration $moderation)
	{
		$message = (string) $request->input('message', '');
		$message = trim($message);
		if ($message === '') {
			return response()->json([
				'blocked' => true,
				'reasons' => ['Empty message'],
				'reply' => null,
			]);
		}

		// Check for hate speech or disallowed content in user input
		$moderationResult = $moderation->checkForHateSpeech($message);
		if (!empty($moderationResult['isHateSpeech'])) {
			return response()->json([
				'blocked' => true,
				'reasons' => $moderationResult['reasons'] ?? ['Content blocked'],
				'reply' => null,
			]);
		}

		$apiKey = config('services.gemini.key');
		if (empty($apiKey)) {
			return response()->json([
				'blocked' => true,
				'reasons' => ['Gemini API key is not configured'],
				'reply' => null,
			]);
		}

		// Maintain a minimal chat history in session for context (last 8 turns)
		$history = (array) Session::get('aspiro_chat_history', []);
		$history[] = ['role' => 'user', 'content' => $message];
		if (count($history) > 16) {
			$history = array_slice($history, -16);
		}

		$systemPrompt = <<<EOT
Kamu adalah Aspiro, asisten politik netral yang memberi jawaban singkat, menarik, dan berbasis fakta.

Prinsip Jawaban:
- Bahasa Indonesia, ≤100 kata. Utamakan 2–3 kalimat atau 3 poin maksimum.
- Mulai dengan 1 kalimat takeaway yang jelas (hook), lalu dukung dengan poin/kalimat ringkas.
- Netral dan adil: tampilkan sudut pandang/pro–kontra dan trade-off utama.
- Nyatakan ketidakpastian bila data terbatas; jangan buat klaim tanpa dasar.
- Sebut tipe sumber (laporan resmi, riset, media kredibel) tanpa membuat URL.
- Tutup dengan 1 pertanyaan tindak lanjut singkat yang memancing diskusi.

Batasan & Keamanan:
- Tolak propaganda, ajakan kampanye, atau serangan personal; arahkan ke substansi kebijakan.
- Hindari ujaran kebencian/hasutan; patuhi keselamatan konten.
- Jika diminta update real-time, beri disclaimer singkat soal keterbatasan waktu.
- Bukan nasihat hukum/medis; arahkan ke profesional bila relevan.

Format Output - PENTING: JANGAN GUNAKAN FORMAT MARKDOWN (*, **, -, #, dll):
- Gunakan format teks biasa tanpa simbol markdown
- Untuk poin-poin, gunakan format seperti "Pertama, ... Kedua, ... Ketiga, ..." atau "Di satu sisi, ... Di sisi lain, ..."
- Atau gunakan format paragraf biasa dengan kalimat yang mengalir natural
- Maksimal 3 poin utama dengan penjelasan singkat

Nada: sopan, to the point, engaging tanpa hiperbola, tetap netral. Gunakan bahasa percakapan yang natural seperti sedang berbicara dengan teman.
EOT;

		// Build Gemini request with structured history (improves relevance)
		$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

		$contents = [];
		$contents[] = [
			'role' => 'user',
			'parts' => [ ['text' => $systemPrompt] ],
		];
		foreach ($history as $turn) {
			$role = ($turn['role'] ?? 'user') === 'assistant' ? 'model' : 'user';
			$contents[] = [
				'role' => $role,
				'parts' => [ ['text' => (string) ($turn['content'] ?? '')] ],
			];
		}

		$requestBody = [
			'contents' => $contents,
			'safetySettings' => [
				[ 'category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE' ],
				[ 'category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE' ],
				[ 'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE' ],
				[ 'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE' ],
			],
		];

		try {
			$response = Http::timeout(20)
				->withQueryParameters(['key' => $apiKey])
				->post($endpoint, $requestBody);

			if (!$response->successful()) {
				Log::warning('Gemini chat API error', [
					'status' => $response->status(),
					'body' => $response->body(),
				]);
				return response()->json([
					'blocked' => true,
					'reasons' => ['Failed to get response from model'],
					'reply' => null,
				]);
			}

			$data = $response->json();
			$modelText = '';
			if (!empty($data['candidates'][0]['content']['parts'])) {
				foreach ($data['candidates'][0]['content']['parts'] as $part) {
					if (isset($part['text'])) {
						$modelText .= $part['text'];
					}
				}
			}
			$modelText = trim((string) $modelText);

			if ($modelText === '') {
				return response()->json([
					'blocked' => true,
					'reasons' => ['Empty response from model'],
					'reply' => null,
				]);
			}

			// Clean up markdown formatting for more natural responses
			$modelText = $this->cleanMarkdownFormatting($modelText);

			$history[] = ['role' => 'assistant', 'content' => $modelText];
			if (count($history) > 16) {
				$history = array_slice($history, -16);
			}
			Session::put('aspiro_chat_history', $history);

			return response()->json([
				'blocked' => false,
				'reasons' => [],
				'reply' => $modelText,
			]);
		} catch (\Throwable $e) {
			Log::error('AspiroBot chat error', ['error' => $e->getMessage()]);
			return response()->json([
				'blocked' => true,
				'reasons' => ['Unexpected error'],
				'reply' => null,
			]);
		}
	}

	/**
	 * Reset the chat session history.
	 */
	public function reset(Request $request)
	{
		Session::forget('aspiro_chat_history');
		return response()->json(['ok' => true]);
	}

	/**
	 * Clean markdown formatting from AI response to make it more natural.
	 */
	private function cleanMarkdownFormatting(string $text): string
	{
		// Remove bold formatting (**text** or __text__)
		$text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);
		$text = preg_replace('/__(.*?)__/', '$1', $text);
		
		// Remove italic formatting (*text* or _text_)
		$text = preg_replace('/\*(.*?)\*/', '$1', $text);
		$text = preg_replace('/_(.*?)_/', '$1', $text);
		
		// Remove bullet points (* or - at start of line)
		$text = preg_replace('/^\s*[\*\-\+]\s+/m', '', $text);
		
		// Remove headers (# ## ###)
		$text = preg_replace('/^#+\s*/m', '', $text);
		
		// Remove code blocks (```code```)
		$text = preg_replace('/```.*?```/s', '', $text);
		
		// Remove inline code (`code`)
		$text = preg_replace('/`([^`]+)`/', '$1', $text);
		
		// Remove links [text](url)
		$text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);
		
		// Clean up multiple spaces and newlines
		$text = preg_replace('/\s+/', ' ', $text);
		$text = preg_replace('/\n\s*\n/', "\n\n", $text);
		
		// Trim whitespace
		$text = trim($text);
		
		return $text;
	}
}
