<?php

namespace App\Domain\Feature\Exception;

use RuntimeException;

class RuleNotFoundException extends RuntimeException
{
    public function __construct(int $id) {
        parent::__construct("Rule with id {$id} not found");
    }
}
