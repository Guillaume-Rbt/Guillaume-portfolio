<?php

namespace App\Http\Admin\api\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateFolderPayload
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        public ?string $parent = null,
    ) {
    }
}
