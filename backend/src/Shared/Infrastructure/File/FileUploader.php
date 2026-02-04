<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileUploader
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_SIZE = 2 * 1024 * 1024;

    public function __construct(
        private readonly string $projectDir
    ) {
    }

    public function upload(UploadedFile $file, string $subdir): string
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Invalid or missing file');
        }

        $mime = $file->getMimeType();
        if ($mime === false || !\in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new \InvalidArgumentException('Allowed types: JPEG, PNG, WebP');
        }

        if ($file->getSize() > self::MAX_SIZE) {
            throw new \InvalidArgumentException('Max size 2MB');
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        $dir = $this->projectDir . '/public/uploads/' . $subdir;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $file->move($dir, $filename);
        } catch (FileException $e) {
            throw new \InvalidArgumentException('Upload failed: ' . $e->getMessage());
        }

        return 'uploads/' . $subdir . '/' . $filename;
    }
}
