<?php

namespace Squirrel\ValidatorCascade\Tests;

use Squirrel\ValidatorCascade\Cascade;
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

    public function testBadTriggerValue()
    {
        $this->expectException(UnexpectedTypeException::class);

        $cascadeValidator = new CascadeValidator();

        $constraint = new Cascade([
            'trigger' => false,
        ]);

        $cascadeValidator->validate('somevalue', $constraint);
    }

    public function testBadTriggerValue2()
    {
        $this->expectException(UnexpectedTypeException::class);

        $cascadeValidator = new CascadeValidator();

        $constraint = new Cascade([
            'trigger' => 2,
        ]);

        $cascadeValidator->validate('somevalue', $constraint);
    }

    public function testBadTriggerValue3()
    {
        $this->expectException(UnexpectedTypeException::class);

        $cascadeValidator = new CascadeValidator();

        $constraint = new Cascade([
            'trigger' => true,
        ]);

        $cascadeValidator->validate('somevalue', $constraint);
    }

    public function testBadTriggerValue4()
    {
        $this->expectException(UnexpectedTypeException::class);

        $cascadeValidator = new CascadeValidator();

        $constraint = new Cascade([
            'trigger' => new \stdClass(),
        ]);

        $cascadeValidator->validate('somevalue', $constraint);
    }

    public function testBadTriggerValue5()
    {
        $this->expectException(UnexpectedTypeException::class);

        $cascadeValidator = new CascadeValidator();

        $constraint = new Cascade([
            'trigger' => function () {
            },
        ]);

        $cascadeValidator->validate('somevalue', $constraint);
    }
}
