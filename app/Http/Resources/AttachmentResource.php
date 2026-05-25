<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'original_name' => $this->original_name,
            'mime_type'     => $this->mime_type,
            'size_bytes'    => $this->size_bytes,
            'stream_url'    => url('/api/v1/attachments/'.$this->id.'/stream'),
            'created_at'    => $this->created_at?->toISOString(),
        ];
    }
}
