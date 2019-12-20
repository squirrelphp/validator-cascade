<?php

namespace Squirrel\ValidatorCascade\Examples;

use Squirrel\ValidatorCascade\Cascade;
use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    /**
     * Validate $shippingAddress if validation with no validation
     * group or the "Default" validation group is triggered
     *
     * Validates "Default" and "phoneNumberMandatory" validation groups
     * in $shippingAddress
     *
     * @Assert\NotNull()
     * @Assert\Type(type="Squirrel\ValidatorCascade\Examples\Address")
     * @Cascade(trigger={"Default", "phoneNumberMandatory"})
     *
     * @var Address
     */
    public $shippingAddress;

    /**
     * Validate $invoiceAddress only if validation group
     * "alternateInvoiceAddress" is passed to validator
     *
     * Validates only "Default" validation group in $invoiceAddress,
     * so phone number is optional
     *
     * @Assert\NotNull()
     * @Assert\Type(type="Squirrel\ValidatorCascade\Examples\Address")
     * @Cascade(groups="alternateInvoiceAddress")
     *
     * @var Address
     */
    public $invoiceAddress;

    /**
     * Validates only the phone number in the address, as the Default group
     * is not passed in
     *
     * @Assert\NotNull()
     * @Assert\Type(type="Squirrel\ValidatorCascade\Examples\Address")
     * @Cascade(trigger="phoneNumberMandatory")
     *
     * @var Address
     */
    public $phoneNumberOnlyAddress;
}
