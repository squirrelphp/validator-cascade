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
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Cascade');
        }

        // Ignore null values, as there is nothing to cascade validation to
        if ($value === null) {
            return;
        }

        // Convert string to array if a string was given for trigger validation groups
        if (\is_string($constraint->trigger)) {
            $constraint->trigger = [$constraint->trigger];
        }

        // At this point we require an array, otherwise something went wrong
        if (!\is_array($constraint->trigger)) {
            throw new UnexpectedTypeException($constraint->trigger, 'array|string');
        }

        $this->context
            ->getValidator()
            ->inContext($this->context)
            ->validate($value, null, $constraint->trigger);
    }
}
