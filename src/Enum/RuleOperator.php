<?php

namespace App\Enum;

enum RuleOperator: string
{
    case EQUALS = 'equals';
    case IN = 'in';
    case GREATER_THAN = 'greater_than';
    case LESS_THAN = 'less_than';
}
