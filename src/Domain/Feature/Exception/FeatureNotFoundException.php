<?php

namespace App\Domain\Feature\Exception;

use RuntimeException;

class FeatureNotFoundException extends RuntimeException
{
    public function __construct(string $name) {
        parent::__construct("Feature {$name} not found");
    }
}
