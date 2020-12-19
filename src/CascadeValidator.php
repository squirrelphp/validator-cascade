<?php

namespace Squirrel\ValidatorCascade;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CascadeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Cascade) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Cascade');
        }

        // Ignore null values, as there is nothing to cascade validation to
        if ($value === null) {
            return;
        }

        $this->context
            ->getValidator()
            ->inContext($this->context)
            ->validate($value, null, $constraint->getTrigger());
    }
}
