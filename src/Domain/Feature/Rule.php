<?php

namespace App\Domain\Feature;

use App\Domain\Feature\RuleOperator;

final class Rule
{
    public function __construct(
        private int $id,
        private string $type,
        private string $value,
        private RuleOperator $operator
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
