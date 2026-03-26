<?php

namespace App\Domain\Feature;

use App\Domain\Feature\RuleOperator;

final class Rule
{
    public function __construct(
        private string $type,
        private string $value,
        private RuleOperator $operator,
        private ?int $id = null
    ) {}

    public function id(): int {
        return $this->id;
    }

    public function type(): string {
        return $this->type;
    }

    public function value(): string {
        return $this->value;
    }

    public function operator(): RuleOperator {
        return $this->operator;
    }
}
