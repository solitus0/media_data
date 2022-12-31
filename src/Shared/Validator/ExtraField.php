<?php

declare(strict_types=1);

namespace App\Shared\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ExtraField extends Constraint
{
    public const EXTRA_FIELD = 'validator.extra_fields_are_not_allowed';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
