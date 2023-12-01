<?php

namespace Squirrel\ValidatorCascade\Tests;

use Squirrel\ValidatorCascade\Examples\Address;
use Squirrel\ValidatorCascade\Examples\Order;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderTest extends \PHPUnit\Framework\TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = \Symfony\Component\Validator\Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testWithDifferentGroups(): void
    {
        $order = new Order();
        $order->shippingAddress = new Address();
        $order->invoiceAddress = new Address();
        $order->phoneNumberOnlyAddress = new Address();

        $expectedViolations = [
            'shippingAddress.street' => Length::class,
            'shippingAddress.city' => Length::class,
            'shippingAddress.phoneNumber' => Length::class,
            'phoneNumberOnlyAddress.phoneNumber' => Length::class,
        ];

        $validationResults = $this->validator->validate($order);
        $this->assertEquals(\count($expectedViolations), $validationResults->count());

        foreach ($validationResults as $validationResult) {
            $this->assertTrue(isset($expectedViolations[$validationResult->getPropertyPath()]));
            $this->assertSame($expectedViolations[$validationResult->getPropertyPath()], \get_class($validationResult->getConstraint()));

            unset($expectedViolations[$validationResult->getPropertyPath()]);
        }

        $expectedViolations = [
            'shippingAddress.street' => Length::class,
            'shippingAddress.city' => Length::class,
            'shippingAddress.phoneNumber' => Length::class,
            'invoiceAddress.street' => Length::class,
            'invoiceAddress.city' => Length::class,
            'phoneNumberOnlyAddress.phoneNumber' => Length::class,
        ];

        $validationResults = $this->validator->validate($order, groups: ['Default', 'alternateInvoiceAddress']);
        $this->assertEquals(\count($expectedViolations), $validationResults->count());

        foreach ($validationResults as $validationResult) {
            $this->assertTrue(isset($expectedViolations[$validationResult->getPropertyPath()]));
            $this->assertSame($expectedViolations[$validationResult->getPropertyPath()], \get_class($validationResult->getConstraint()));

            unset($expectedViolations[$validationResult->getPropertyPath()]);
        }

        $expectedViolations = [
            'invoiceAddress.street' => Length::class,
            'invoiceAddress.city' => Length::class,
        ];

        $validationResults = $this->validator->validate($order, groups: ['alternateInvoiceAddress']);
        $this->assertEquals(\count($expectedViolations), $validationResults->count());

        foreach ($validationResults as $validationResult) {
            $this->assertTrue(isset($expectedViolations[$validationResult->getPropertyPath()]));
            $this->assertSame($expectedViolations[$validationResult->getPropertyPath()], \get_class($validationResult->getConstraint()));

            unset($expectedViolations[$validationResult->getPropertyPath()]);
        }
    }

    public function testWithNullValue(): void
    {
        $order = new Order();
        $order->shippingAddress = new Address();
        $order->invoiceAddress = null;
        $order->phoneNumberOnlyAddress = new Address();

        $expectedViolations = [
            'shippingAddress.street' => Length::class,
            'shippingAddress.city' => Length::class,
            'shippingAddress.phoneNumber' => Length::class,
            'invoiceAddress' => NotNull::class,
            'phoneNumberOnlyAddress.phoneNumber' => Length::class,
        ];

        $validationResults = $this->validator->validate($order, groups: ['Default', 'alternateInvoiceAddress']);
        $this->assertEquals(\count($expectedViolations), $validationResults->count());

        foreach ($validationResults as $validationResult) {
            $this->assertTrue(isset($expectedViolations[$validationResult->getPropertyPath()]));
            $this->assertSame($expectedViolations[$validationResult->getPropertyPath()], \get_class($validationResult->getConstraint()));

            unset($expectedViolations[$validationResult->getPropertyPath()]);
        }
    }
}
