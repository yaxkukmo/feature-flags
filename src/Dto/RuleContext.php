<?php

namespace App\Dto;

class RuleContext
{
    public function __construct(
        public readonly ?int $userId,
        public readonly ?string $country = null,
        public readonly ?string $plan = null,
    ) {}
}
