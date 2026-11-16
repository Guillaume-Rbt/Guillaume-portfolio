<?php

namespace App\Http\Admin\api\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RenamePayload
{
    public function __construct(
        #[Assert\NotBlank]
        public string $id,
        #[Assert\NotBlank]
        public string $name,
    ) {
    }
}
