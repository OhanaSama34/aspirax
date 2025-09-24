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
- Bahasa Indonesia, ≤100 kata. Utamakan 2–3 kalimat atau 3 bullet maksimum.
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

Format Output (pilih salah satu, yang paling ringkas terhadap pertanyaan pengguna):
- 2–3 kalimat, atau
- Maks. 3 bullet: (1) inti isu, (2) pro, (3) kontra/trade-off.

Nada: sopan, to the point, engaging tanpa hiperbola, tetap netral.
EOT;

		// Build Gemini request with structured history (improves relevance)
		$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

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
}
