<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RuleContext
{
    public function __construct(
        #[Assert\Positive]
        public readonly ?int $userId,
        #[Assert\Length(min: 2, max: 255)]
        public readonly ?string $country = null,
        #[Assert\Length(min: 2, max: 255)]
        public readonly ?string $plan = null,
    ) {}
}
