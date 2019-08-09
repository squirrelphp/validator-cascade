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

        // Leads to six violations in $shippingAddress, none in $invoiceAddress, two in $phoneNumberOnlyAddress
        $this->assertEquals(8, \count($this->validator->validate($order)));

        // Leads to six violations in $shippingAddress, four in $invoiceAddress, two in $phoneNumberOnlyAddress
        $this->assertEquals(12, \count($this->validator->validate($order, null, ['Default', 'alternateInvoiceAddress'])));

        // Leads to no violations in $shippingAddress, four in $invoiceAddress, none in $phoneNumberOnlyAddress
        $this->assertEquals(4, \count($this->validator->validate($order, null, ['alternateInvoiceAddress'])));
    }

    public function testWithNull()
    {
        $order = new Order();
        $order->shippingAddress = null;
        $order->invoiceAddress = null;

        // Leads to three NotNull violations
        $this->assertEquals(3, \count($this->validator->validate($order)));
    }
}
