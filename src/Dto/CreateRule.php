<?php

namespace App\Dto;

use App\Domain\Feature\RuleOperator;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateRule
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $type,
        #[Assert\NotBlank]
        public readonly string $value,
        public readonly RuleOperator $operator,
    ) { }
}
