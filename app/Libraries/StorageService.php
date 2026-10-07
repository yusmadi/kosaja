<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * StorageService
 * Manages image uploads with dual support:
 * 1. Local Storage (`public/uploads/...`)
 * 2. Cloud Object Storage (AWS S3, Cloudflare R2, MinIO) via AWS SigV4
 */
class StorageService
{
    protected string $driver;
    protected string $s3Bucket;
    protected string $s3Key;
    protected string $s3Secret;
    protected string $s3Region;
    protected string $s3Endpoint;

    public function __construct()
    {
        // Read configuration from environment (.env)
        $this->driver     = env('STORAGE_DRIVER', 'local');
        $this->s3Bucket   = env('S3_BUCKET', '');
        $this->s3Key      = env('S3_KEY', '');
        $this->s3Secret   = env('S3_SECRET', '');
        $this->s3Region   = env('S3_REGION', 'ap-southeast-1');
        $this->s3Endpoint = env('S3_ENDPOINT', ''); // e.g. s3.amazonaws.com or https://<account_id>.r2.cloudflarestorage.com
    }

    /**
     * Upload an uploaded file to storage (local or cloud)
     *
     * @param UploadedFile $file
     * @param string $folder Subfolder e.g. 'properties', 'units', 'kyc'
     * @param int $maxSizeKb Maximum size in kilobytes (default 5MB)
     * @return array [success => bool, url => string|null, error => string|null]
     */
    public function uploadImage(UploadedFile $file, string $folder = 'properties', int $maxSizeKb = 5120): array
    {
        if (! $file->isValid()) {
            return [
                'success' => false,
                'url'     => null,
                'error'   => $file->getErrorString(),
            ];
        }

        // Validate MIME type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $mime = $file->getMimeType();
        if (! in_array($mime, $allowedTypes, true)) {
            return [
                'success' => false,
                'url'     => null,
                'error'   => 'Format file tidak valid (' . esc($mime) . '). Hanya gambar JPEG, PNG, atau WebP yang diperbolehkan.',
            ];
        }

        // Validate file size
        if ($file->getSizeByUnit('kb') > $maxSizeKb) {
            return [
                'success' => false,
                'url'     => null,
                'error'   => 'Ukuran file melebihi batas maksimal ' . ($maxSizeKb / 1024) . ' MB.',
            ];
        }

        // Generate unique safe filename
        $extension = $file->guessExtension() ?? 'jpg';
        $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $targetSubPath = 'uploads/' . trim($folder, '/') . '/' . $newName;

        // If driver is S3 and credentials are fully provided
        if ($this->driver === 's3' && !empty($this->s3Key) && !empty($this->s3Secret) && !empty($this->s3Bucket)) {
            $cloudResult = $this->uploadToS3($file->getTempName(), $targetSubPath, $mime);
            if ($cloudResult['success']) {
                return [
                    'success' => true,
                    'url'     => $cloudResult['url'],
                    'error'   => null,
                ];
            }
            // If cloud fails, fallback to local storage
            log_message('warning', 'Cloud Object Storage upload failed, falling back to local: ' . ($cloudResult['error'] ?? ''));
        }

        // Local Storage
        $targetDir = FCPATH . 'uploads/' . trim($folder, '/');
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        try {
            $file->move($targetDir, $newName);
            return [
                'success' => true,
                'url'     => $targetSubPath,
                'error'   => null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'url'     => null,
                'error'   => 'Gagal menyimpan file ke server: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete an existing image from storage
     */
    public function deleteImage(string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // If stored as local relative path
        if (str_starts_with($path, 'uploads/')) {
            $fullPath = FCPATH . $path;
            if (file_exists($fullPath)) {
                return @unlink($fullPath);
            }
        }

        // If stored in S3
        if ($this->driver === 's3' && !empty($this->s3Key) && str_starts_with($path, 'http')) {
            $parsed = parse_url($path);
            $key = ltrim($parsed['path'] ?? '', '/');
            return $this->deleteFromS3($key);
        }

        return false;
    }

    /**
     * Format public URL for an asset
     */
    public static function getUrl(?string $path, string $defaultPlaceholder = 'assets/images/sample-kos-1.jpg'): string
    {
        if (empty($path)) {
            return base_url($defaultPlaceholder);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return base_url($path);
    }

    /**
     * Direct S3/R2/MinIO upload using AWS SigV4 REST API
     */
    protected function uploadToS3(string $tempFilePath, string $s3ObjectKey, string $contentType): array
    {
        $fileData = file_get_contents($tempFilePath);
        if ($fileData === false) {
            return ['success' => false, 'error' => 'Gagal membaca temporary file'];
        }

        $host = !empty($this->s3Endpoint)
            ? str_replace(['http://', 'https://'], '', $this->s3Endpoint)
            : "{$this->s3Bucket}.s3.{$this->s3Region}.amazonaws.com";

        $url = (!empty($this->s3Endpoint) ? rtrim($this->s3Endpoint, '/') . '/' . $this->s3Bucket . '/' : "https://{$host}/") . $s3ObjectKey;

        // AWS SigV4 calculation
        $service = 's3';
        $region = $this->s3Region;
        $amzDate = gmdate('Ymd\THis\Z');
        $dateStamp = gmdate('Ymd');
        $contentPayloadHash = hash('sha256', $fileData);

        $canonicalUri = '/' . (!empty($this->s3Endpoint) ? $this->s3Bucket . '/' : '') . $s3ObjectKey;
        $canonicalHeaders = "content-type:{$contentType}\nhost:{$host}\nx-amz-content-sha256:{$contentPayloadHash}\nx-amz-date:{$amzDate}\n";
        $signedHeaders = "content-type;host;x-amz-content-sha256;x-amz-date";
        $canonicalRequest = "PUT\n{$canonicalUri}\n\n{$canonicalHeaders}\n{$signedHeaders}\n{$contentPayloadHash}";

        $algorithm = 'AWS4-HMAC-SHA256';
        $credentialScope = "{$dateStamp}/{$region}/{$service}/aws4_request";
        $stringToSign = "{$algorithm}\n{$amzDate}\n{$credentialScope}\n" . hash('sha256', $canonicalRequest);

        // Signing key calculation
        $kDate = hash_hmac('sha256', $dateStamp, 'AWS4' . $this->s3Secret, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', $service, $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        $authorizationHeader = "{$algorithm} Credential={$this->s3Key}/{$credentialScope}, SignedHeaders={$signedHeaders}, Signature={$signature}";

        $headers = [
            "Host: {$host}",
            "x-amz-date: {$amzDate}",
            "x-amz-content-sha256: {$contentPayloadHash}",
            "Content-Type: {$contentType}",
            "Authorization: {$authorizationHeader}",
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'url' => $url];
        }

        return ['success' => false, 'error' => "S3 error (HTTP {$httpCode}): {$response} {$curlError}"];
    }

    /**
     * Delete from S3
     */
    protected function deleteFromS3(string $s3ObjectKey): bool
    {
        // Minimal stub for cloud delete
        return true;
    }
}
