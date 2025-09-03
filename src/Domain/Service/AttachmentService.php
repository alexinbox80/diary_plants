<?php

namespace App\Domain\Service;

use App\Domain\Entity\Attachment;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentService
{
    public function createAttachment(
        UploadedFile $file,
        string $targetType,
        int $targetId
    ): ?Attachment
    {
        $filename = uniqid() . '-' . $file->getClientOriginalName();
        $file->move('uploads/attachments', $filename);

//        $attachment = new Attachment();
//        $attachment->setFilename($filename);
//        $attachment->setPath('uploads/attachments/' . $filename);
//        $attachment->setTargetType($targetType);
//        $attachment->setTargetId($targetId);

        return null;
    }
}
