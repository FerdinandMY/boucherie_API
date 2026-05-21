<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @group Pièces jointes audio
 * @authenticated
 */
class AttachmentController extends Controller
{
    public function __construct(private readonly AttachmentService $service) {}

    /**
     * Envoyer un fichier audio
     *
     * @response 201 {"data":{"id":"uuid","original_name":"note.webm","mime_type":"audio/webm","size_bytes":12345,"stream_url":"https://.../api/v1/attachments/{id}/stream","created_at":"..."},"message":"Fichier enregistré."}
     */
    public function store(StoreAttachmentRequest $request): JsonResponse
    {
        $this->authorize('create', Attachment::class);

        $attachment = $this->service->storeUpload(
            $request->file('file'),
            $request->user(),
        );

        return response()->json([
            'data'    => new AttachmentResource($attachment),
            'message' => 'Fichier enregistré.',
        ], 201);
    }

    /**
     * Lire / télécharger un fichier audio (authentification requise).
     */
    public function stream(string $id): StreamedResponse
    {
        $attachment = $this->service->findForStream($id);
        $this->authorize('view', $attachment);

        return Storage::disk($attachment->disk)->response(
            $attachment->path,
            $attachment->original_name,
            ['Content-Type' => $attachment->mime_type],
        );
    }
}
