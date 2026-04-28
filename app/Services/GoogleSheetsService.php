<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Lightweight Google Sheets service using Service Account JWT auth.
 * Requires no extra Composer packages — only Guzzle (bundled with Laravel).
 */
class GoogleSheetsService
{
    private Client $http;
    private string $spreadsheetId;
    private string $sheetName;

    public function __construct()
    {
        $this->http          = new Client(['timeout' => 10]);
        $this->spreadsheetId = config('services.google_sheets.spreadsheet_id', '');
        $this->sheetName     = config('services.google_sheets.sheet_name', 'RSVP');
    }

    /**
     * Append a single row of values to the sheet.
     *
     * @param  array<int, string|int|bool|null>  $row
     */
    public function appendRow(array $row): bool
    {
        if (empty($this->spreadsheetId)) {
            return false;
        }

        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return false;
            }

            $range = urlencode("{$this->sheetName}!A1");
            $url   = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$range}:append?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS";

            $this->http->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'values' => [$row],
                ],
            ]);

            return true;
        } catch (GuzzleException $e) {
            Log::warning('GoogleSheets appendRow failed: ' . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------------------
    // JWT / OAuth helpers
    // -------------------------------------------------------------------------

    private function getAccessToken(): ?string
    {
        // Priority 1: credentials from environment variable (JSON string) — works on Render / cloud hosts
        $credentialsJson = config('services.google_sheets.credentials_json', '');
        if (!empty($credentialsJson)) {
            $credentials = json_decode($credentialsJson, true);
            if ($credentials && ($credentials['type'] ?? '') === 'service_account') {
                return $this->fetchTokenFromServiceAccount($credentials);
            }
            Log::warning('GoogleSheets: GOOGLE_SHEETS_CREDENTIALS_JSON không hợp lệ (không phải service_account JSON).');
            return null;
        }

        // Priority 2: credentials from file path
        $credentialsPath = config('services.google_sheets.credentials_path', '');

        if (empty($credentialsPath)) {
            Log::warning('GoogleSheets: Chưa cấu hình GOOGLE_SHEETS_CREDENTIALS_JSON hoặc GOOGLE_SHEETS_CREDENTIALS_PATH trong .env');
            return null;
        }

        if (!file_exists($credentialsPath)) {
            Log::warning("GoogleSheets: Không tìm thấy file credentials tại [{$credentialsPath}]");
            return null;
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);

        if (!$credentials || ($credentials['type'] ?? '') !== 'service_account') {
            Log::warning('GoogleSheets: invalid service account credentials file.');
            return null;
        }

        return $this->fetchTokenFromServiceAccount($credentials);
    }

    /**
     * Exchange service-account credentials for a short-lived Bearer token.
     */
    private function fetchTokenFromServiceAccount(array $credentials): ?string
    {
        $now        = time();
        $expiry     = $now + 3600;
        $scope      = 'https://www.googleapis.com/auth/spreadsheets';
        $tokenUrl   = $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token';
        $clientEmail = $credentials['client_email'] ?? '';
        $privateKey  = $credentials['private_key'] ?? '';

        if (empty($clientEmail) || empty($privateKey)) {
            return null;
        }

        // Build JWT header + payload
        $header  = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = $this->base64UrlEncode(json_encode([
            'iss'   => $clientEmail,
            'scope' => $scope,
            'aud'   => $tokenUrl,
            'exp'   => $expiry,
            'iat'   => $now,
        ]));

        $signingInput = "{$header}.{$payload}";
        $signature    = '';

        if (!openssl_sign($signingInput, $signature, $privateKey, 'SHA256')) {
            Log::warning('GoogleSheets: failed to sign JWT.');
            return null;
        }

        $jwt = "{$signingInput}." . $this->base64UrlEncode($signature);

        try {
            $response = $this->http->post($tokenUrl, [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return $data['access_token'] ?? null;
        } catch (GuzzleException $e) {
            Log::warning('GoogleSheets: token exchange failed: ' . $e->getMessage());
            return null;
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
