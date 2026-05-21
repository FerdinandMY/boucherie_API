<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Services\AttachmentService;
use Illuminate\Database\Eloquent\Model;

trait LinksAttachments
{
    /**
     * @param  array<string, mixed>  $validated
     */
    protected function stripAttachmentIds(array &$validated): array
    {
        $ids = $validated['attachment_ids'] ?? [];
        unset($validated['attachment_ids']);

        return is_array($ids) ? $ids : [];
    }

    protected function linkAttachments(Model $model, array $attachmentIds, User $user): Model
    {
        if ($attachmentIds === []) {
            return $model;
        }

        app(AttachmentService::class)->linkTo($model, $attachmentIds, $user);

        return $model->load('attachments');
    }
}
