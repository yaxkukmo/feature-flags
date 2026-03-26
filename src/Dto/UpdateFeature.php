<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateFeature
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public readonly string $name,
        public readonly bool $enabled,
        #[Assert\Range(min: 0, max: 100)]
        public readonly ?int $rolloutPercentage
    ) { }
}
