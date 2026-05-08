<?php

namespace App\Http\Controllers\School;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreExpedienteDocumentoRequest;
use App\Http\Resources\School\ExpedienteDocumentoResource;
use App\Models\School\ExpedienteDocumento;
use App\Services\School\ExpedienteDocumentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\School\ValidateExpedienteDocumentoRequest;

class ExpedienteDocumentoController extends Controller
{
    public function __construct(
        private ExpedienteDocumentoService $expedienteDocumentoService
    ) {}

    public function index(Request $request): JsonResponse {
        $perPage = $request->integer('per_page',15);

        $documentos = ExpedienteDocumento::query()
            ->with([
                'tipoDocumento',
            ])
            ->when($request->filled('expediente_id'),
                function ($query) use ($request) {
                    $query->where('expediente_id', $request->expediente_id);
                }
            )
            ->latest()
            ->paginate($perPage);

        return ResponseHelper::success(
            ExpedienteDocumentoResource::collection(
                $documentos
            ),
            'Documentos obtenidos correctamente.'
        );
    }

    public function store(StoreExpedienteDocumentoRequest $request): JsonResponse {
        $documento = $this->expedienteDocumentoService
            ->create(
                $request->validated()
            );

        return ResponseHelper::success(
            new ExpedienteDocumentoResource(
                $documento
            ),
            'Documento creado correctamente.',
            201
        );
    }

    public function destroy(ExpedienteDocumento $expedienteDocumento): JsonResponse {
        $this->expedienteDocumentoService
            ->delete($expedienteDocumento);

        return ResponseHelper::success(
            null,
            'Documento eliminado correctamente.'
        );
    }

    public function download(ExpedienteDocumento $expedienteDocumento) {
        $path = $expedienteDocumento->file_path;

        if (! $path) {
            return ResponseHelper::error(
                'Archivo no encontrado.',
                404
            );
        }

        // FTP
        if (config('storage.type') == 'ftp') {
            return $this->downloadFromFtp($expedienteDocumento);
        }

        // LOCAL
        if (! Storage::disk('local')->exists($path)) {
            return ResponseHelper::error(
                'Archivo no encontrado.',
                404
            );
        }

        return Storage::disk('local')
            ->download(
                $path,
                $expedienteDocumento->original_name
            );
    }

    private function downloadFromFtp(ExpedienteDocumento $expedienteDocumento) {
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
            return ResponseHelper::error(
                'Error conexión FTP.',
                500
            );
        }

        $temp = tmpfile();
        $meta = stream_get_meta_data($temp);
        $tempPath = $meta['uri'];

        $remotePath =
            '/private/' .
            $expedienteDocumento->file_path;

        $result = ftp_get(
            $ftpConnection,
            $tempPath,
            $remotePath,
            FTP_BINARY
        );

        ftp_close($ftpConnection);

        if (! $result) {
            fclose($temp);

            return ResponseHelper::error(
                'Archivo no encontrado.',
                404
            );
        }

        return response()->download(
            $tempPath,
            $expedienteDocumento->original_name,
            [
                'Content-Type' =>
                    $expedienteDocumento->mime_type,
            ]
        )->deleteFileAfterSend(true);
    }

    public function validateDocument(ValidateExpedienteDocumentoRequest $request, ExpedienteDocumento $expedienteDocumento): JsonResponse {
        $documento = $this->expedienteDocumentoService
            ->validateDocument(
                $expedienteDocumento,
                $request->validated()
            );

        return ResponseHelper::success(
            new ExpedienteDocumentoResource(
                $documento
            ),
            'Documento validado correctamente.'
        );
    }
}
