<?php

namespace Squirrel\ValidatorCascade\Tests;

use Squirrel\ValidatorCascade\Examples\Address;
use Squirrel\ValidatorCascade\Examples\Order;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();

        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
        $annotationReader = new \Doctrine\Common\Annotations\AnnotationReader();

        $this->validator = \Symfony\Component\Validator\Validation::createValidatorBuilder()
            ->enableAnnotationMapping($annotationReader)
            ->getValidator();
    }

    public function testWithDifferentGroups()
    {
        $order = new Order();
        $order->shippingAddress = new Address();
        $order->invoiceAddress = new Address();
        $order->phoneNumberOnlyAddress = new Address();

        $additionalPHP8Violations = 0;

        // Additional violations because of the PHP8 attributes - only affects PHP8
        if (PHP_VERSION_ID >= 80000) {
            $additionalPHP8Violations = 4;
        }

        // PHP7: Leads to six violations in $shippingAddress, none in $invoiceAddress, two in $phoneNumberOnlyAddress
        // PHP8: Leads to eight violations in $shippingAddress, none in $invoiceAddress, four in $phoneNumberOnlyAddress
        $this->assertEquals(8 + $additionalPHP8Violations, \count($this->validator->validate($order)));

        // PHP7: Leads to six violations in $shippingAddress, four in $invoiceAddress, two in $phoneNumberOnlyAddress
        // PHP8: Leads to eight violations in $shippingAddress, four in $invoiceAddress, four in $phoneNumberOnlyAddress
        $this->assertEquals(12 + $additionalPHP8Violations, \count($this->validator->validate($order, null, ['Default', 'alternateInvoiceAddress'])));

        // Leads to no violations in $shippingAddress, four in $invoiceAddress, none in $phoneNumberOnlyAddress
        $this->assertEquals(4, \count($this->validator->validate($order, null, ['alternateInvoiceAddress'])));
    }

    public function testWithNull()
    {
        $order = new Order();
        $order->shippingAddress = null;
        $order->invoiceAddress = null;
        $order->phoneNumberOnlyAddress = null;

        $expectedViolations = 3;

        // Additional violations because of the PHP8 attributes - only affects PHP8
        if (PHP_VERSION_ID >= 80000) {
            $expectedViolations = 4;
        }

        // Leads to NotNull violations
        $this->assertEquals($expectedViolations, \count($this->validator->validate($order)));
    }
}
