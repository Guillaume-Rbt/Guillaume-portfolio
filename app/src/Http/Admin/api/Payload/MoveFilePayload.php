<?php

namespace App\Http\Admin\api\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class MoveFilePayload
{
    public function __construct(
        #[Assert\NotBlank]
        public string $id,
        public ?string $parent = null,
    ) {
    }
}
