<?php

namespace App\Domain\Model\Attachment;

use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

class UpdateAttachmentModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,

        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public readonly bool $isShown,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly ?string $filename = null,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly ?string $path = null,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly ?string $mimeType = null,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $alt,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $title,

        #[Assert\NotBlank]
        public readonly DateTimeImmutable $fileDate,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $attachableId,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $attachableType,

        public readonly ?string $description = null,
    ) {
    }
}
