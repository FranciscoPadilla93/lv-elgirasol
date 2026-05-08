<?php

namespace App\Repositories\Storage;

use Exception;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileRepository
{
    public function getTipoAlmacenamiento(): string
    {
        return config('storage.type','local');
    }

    // LOCAL
    public function putFileAs(
        string $path,
        $file,
        string $disk = 'local'
    ): string {
        $fileName = Str::uuid() . '.' .
            $file->extension();

        $rutaCompletaArchivo =
            $path . '/' . $fileName;

        Storage::disk($disk)
            ->putFileAs(
                $path,
                $file,
                $fileName
            );

        return $rutaCompletaArchivo;
    }

    public function deleteExistingFile(
        ?string $existingFilePath,
        string $disk = 'local'
    ): void {
        if (
            !empty($existingFilePath) &&
            Storage::disk($disk)
                ->exists($existingFilePath)
        ) {

            Storage::disk($disk)
                ->delete($existingFilePath);
        }
    }

    // FTP
    public function putFileAsFtp(
        string $path,
        $file,
        string $remoteDirectory
    ): string {
        $fileName = Str::uuid() . '.' .
            $file->extension();

        $rutaCompletaArchivo =
            $path . '/' . $fileName;

        $archivoStream = fopen(
            $file->getRealPath(),
            'r'
        );

        $ftpServer = config('storage.ftp.host');
        $ftpUsername = config('storage.ftp.username');
        $ftpPassword = config('storage.ftp.password');
        $ftpConnection = ftp_connect($ftpServer);

        ftp_set_option(
            $ftpConnection,
            FTP_TIMEOUT_SEC,
            30
        );

        $ftpLogin = ftp_login(
            $ftpConnection,
            $ftpUsername,
            $ftpPassword
        );

        ftp_pasv($ftpConnection, true);

        if (! $ftpConnection || ! $ftpLogin) {
            throw new Exception(
                'Error al conectar al servidor FTP.',
                500
            );
        }

        $fullPath =
            $remoteDirectory .
            '/' .
            $rutaCompletaArchivo;

        $directory = dirname($fullPath);

        @ftp_mkdir($ftpConnection, $directory);

        $result = ftp_fput(
            $ftpConnection,
            $fullPath,
            $archivoStream,
            FTP_BINARY
        );

        fclose($archivoStream);
        ftp_close($ftpConnection);

        if (! $result) {
            throw new Exception(
                'Error al guardar archivo FTP.',
                500
            );
        }

        return $rutaCompletaArchivo;
    }

    public function deleteExistingFileFtp(
        ?string $existingFilePath,
        string $remoteDirectory
    ): void {
        if (empty($existingFilePath)) {
            return;
        }

        $ftpServer = config('storage.ftp.host');
        $ftpUsername = config('storage.ftp.username');
        $ftpPassword = config('storage.ftp.password');
        $ftpConnection = ftp_connect($ftpServer);

        ftp_set_option(
            $ftpConnection,
            FTP_TIMEOUT_SEC,
            30
        );

        $ftpLogin = ftp_login(
            $ftpConnection,
            $ftpUsername,
            $ftpPassword
        );

        ftp_pasv($ftpConnection, true);

        if (! $ftpConnection || ! $ftpLogin) {
            throw new Exception(
                'Error al conectar al servidor FTP.',
                500
            );
        }

        $fullPath =
            $remoteDirectory .
            '/' .
            $existingFilePath;

        if (
            ftp_size($ftpConnection, $fullPath) != -1
        ) {
            ftp_delete(
                $ftpConnection,
                $fullPath
            );
        }

        ftp_close($ftpConnection);
    }

    // GENERAL
    public function uploadFileStoragePrivate(
        string $path,
        $file,
        ?string $oldFilePath = null
    ): string {
        if (
            $this->getTipoAlmacenamiento()
            == 'ftp'
        ) {
            $rutaCompletaArchivo =
                $this->putFileAsFtp(
                    $path,
                    $file,
                    '/private'
                );

            $this->deleteExistingFileFtp(
                $oldFilePath,
                '/private'
            );

        } else {
            $rutaCompletaArchivo =
                $this->putFileAs(
                    $path,
                    $file,
                    'local'
                );

            $this->deleteExistingFile(
                $oldFilePath,
                'local'
            );
        }

        return $rutaCompletaArchivo;
    }

    // OPTIMIZED IMAGES
    public function putOptimizedContent(
        string $path,
        string $fileName,
        $content,
        string $disk = 'local'
    ): string {
        $rutaCompletaArchivo =
            $path . '/' . $fileName;

        if (
            $this->getTipoAlmacenamiento()
            == 'ftp'
        ) {

            $this->putOptimizedContentFtp(
                $rutaCompletaArchivo,
                $content,
                '/private'
            );
        } else {

            Storage::disk($disk)
                ->put(
                    $rutaCompletaArchivo,
                    $content
                );
        }

        return $rutaCompletaArchivo;
    }

    private function putOptimizedContentFtp(
        string $rutaCompletaArchivo,
        $content,
        string $remoteDirectory
    ): void {
        $ftpServer = config('storage.ftp.host');
        $ftpUsername = config('storage.ftp.username');
        $ftpPassword = config('storage.ftp.password');
        $ftpConnection = ftp_connect($ftpServer);

        ftp_set_option(
            $ftpConnection,
            FTP_TIMEOUT_SEC,
            30
        );

        $ftpLogin = ftp_login(
            $ftpConnection,
            $ftpUsername,
            $ftpPassword
        );

        ftp_pasv($ftpConnection, true);

        if (! $ftpConnection || ! $ftpLogin) {
            throw new Exception(
                'Error al conectar al servidor FTP.',
                500
            );
        }

        $temp = tmpfile();

        fwrite($temp, $content);
        rewind($temp);

        $meta = stream_get_meta_data($temp);
        $tempPath = $meta['uri'];

        $fullPath =
            $remoteDirectory .
            '/' .
            $rutaCompletaArchivo;

        $directory = dirname($fullPath);

        @ftp_mkdir($ftpConnection, $directory);

        $result = ftp_put(
            $ftpConnection,
            $fullPath,
            $tempPath,
            FTP_BINARY
        );

        fclose($temp);
        ftp_close($ftpConnection);

        if (! $result) {

            throw new Exception(
                'Error al guardar archivo optimizado FTP.',
                500
            );
        }
    }
}
