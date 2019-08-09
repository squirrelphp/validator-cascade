Squirrel Validator Cascade
==========================

[![Build Status](https://img.shields.io/travis/com/squirrelphp/validator-cascade.svg)](https://travis-ci.com/squirrelphp/validator-cascade) [![Test Coverage](https://api.codeclimate.com/v1/badges/e056be025c6db0eb31f1/test_coverage)](https://codeclimate.com/github/squirrelphp/validator-cascade/test_coverage) ![PHPStan](https://img.shields.io/badge/style-level%207-success.svg?style=flat-round&label=phpstan) [![Packagist Version](https://img.shields.io/packagist/v/squirrelphp/validator-cascade.svg?style=flat-round)](https://packagist.org/packages/squirrelphp/validator-cascade)  [![PHP Version](https://img.shields.io/packagist/php-v/squirrelphp/validator-cascade.svg)](https://packagist.org/packages/squirrelphp/validator-cascade) [![Software License](https://img.shields.io/badge/license-MIT-success.svg?style=flat-round)](LICENSE)

Reimplements the `Valid` constraint in the Symfony Validator component as `Cascade` annotation which is much more straightforward to use and has no surprising behavior.

Installation
------------

    composer require squirrelphp/validator-cascade

Usage
-----

`Cascade` makes sure an object (or an array of objects) are validated, so it cascades validation.

There are only two options:

- `groups` defines to which validation groups the `Cascade` constraint belongs to, with the same behavior as any regular validator constraint. If you do not define `groups` it is set to `Default` and the `Cascade` constraint is only executed if one of the validation groups matches.

- `trigger` defines which validation groups to trigger on the child object(s). By default only `Default` is triggered, so if you want any other validation groups to trigger you have to specify them with `trigger`. The validation groups of the "parent" are never cascaded.

That is it!

Example
-------

Belong is a common example in real applications: You might have an order and multiple possible addresses for the order (one for shipping, one for invoice) with different requirements, and some addresses should be optional, but if they are specified they should still be validated in full.

`$shippingAddress` shows how to trigger specific validation groups in the child object, in this case to make the phone number a mandatory part of the information (often the case for shipping, but not necessarily for invoice or other address use) in addition to the "Default" constraints.

`$invoiceAddress` is only validated if the validation group "alternateInvoiceAddress" is passed to the validator (which could be done if the user selected an option like "choose different invoice address"). The phone number is optional, as we do not pass the `trigger` option so only the Default group is validated in the Adress object.

```php
use Squirrel\ValidatorCascade\Cascade;
use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    /**
     * Validate $shippingAddress if validation with no validation
     * group or the "Default" validation group is triggered
     *
     * Validates "Default" and "phoneNumberMandatory" validation groups in $shippingAddress
     *
     * @Assert\NotNull()
     * @Assert\Type(type="Address")
     * @Cascade(trigger={"Default", "phoneNumberMandatory"})
     */
    public $shippingAddress;

    /**
     * Validate $invoiceAddress only if validation group
     * "alternateInvoiceAddress" is passed to validator
     *
     * Validates only "Default" validation group in $invoiceAddress, so phone number is optional
     *
     * @Assert\NotNull()
     * @Assert\Type(type="Address")
     * @Cascade(groups={"alternateInvoiceAddress"})
     */
    public $invoiceAddress;
}

class Address
{
    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 1,
     *     max = 50
     * )
     *
     * @var string
     */
    public $street = '';

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 1,
     *     max = 50
     * )
     *
     * @var string
     */
    public $city = '';

    /**
     * @Assert\NotNull(groups={"phoneNumberMandatory"})
     * @Assert\NotBlank(groups={"phoneNumberMandatory"})
     * @Assert\Length(
     *     min = 1,
     *     max = 50
     * )
     *
     * @var string
     */
    public $phoneNumber = '';
}

$order = new Order();
$order->shippingAddress = new Address();
$order->invoiceAddress = new Address();

// This validates with the "Default" validation group,
// so only shippingAddress must be specified
$symfonyValidator->validate($order);

// This also validates the invoice address
$symfonyValidator->validate($order, null, [
    "Default",
    "alternateInvoiceAddress",
]);
```