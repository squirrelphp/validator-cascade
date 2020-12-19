<?php

namespace Squirrel\ValidatorCascade\Tests;

use Squirrel\ValidatorCascade\CascadeValidator;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CascadeValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testWrongConstraint()
    {
        $this->expectException(UnexpectedTypeException::class);

        $cascadeValidator = new CascadeValidator();

        $constraint = new IsTrue();

        $cascadeValidator->validate('somevalue', $constraint);
    }
}
