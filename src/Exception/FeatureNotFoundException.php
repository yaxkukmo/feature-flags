<?php
namespace App\Exception;

use RuntimeException;

class FeatureNotFoundException extends RuntimeException
{
    public function __construct($name) {
        parent::__construct("Feature {$name} not found");
    }
}
