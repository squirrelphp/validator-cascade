Squirrel Validator Cascade
==========================

[![Build Status](https://img.shields.io/travis/com/squirrelphp/validator-cascade.svg)](https://travis-ci.com/squirrelphp/validator-cascade) [![Test Coverage](https://api.codeclimate.com/v1/badges/e056be025c6db0eb31f1/test_coverage)](https://codeclimate.com/github/squirrelphp/validator-cascade/test_coverage) ![PHPStan](https://img.shields.io/badge/style-level%208-success.svg?style=flat-round&label=phpstan) [![Packagist Version](https://img.shields.io/packagist/v/squirrelphp/validator-cascade.svg?style=flat-round)](https://packagist.org/packages/squirrelphp/validator-cascade)  [![PHP Version](https://img.shields.io/packagist/php-v/squirrelphp/validator-cascade.svg)](https://packagist.org/packages/squirrelphp/validator-cascade) [![Software License](https://img.shields.io/badge/license-MIT-success.svg?style=flat-round)](LICENSE)

Reimplements the `Valid` constraint in the Symfony validator component as `Cascade` attribute which is more straightforward to use than `Valid` and has no surprising behavior.

This component is compatible with the Symfony validator component in version 5.x (v2.x with annotation/attribute support) and 6.x/7.x (v3.x with only attribute support) and will be adapted to support future versions of Symfony (if any changes are necessary for that).

Installation
------------

    composer require squirrelphp/validator-cascade

Table of contents
-----------------

- [Usage](#usage)
- [Example](#example)
- [Why not use the Valid constraint?](#why-not-use-the-valid-constraint)

Usage
-----

`Cascade` is a constraint validation which makes sure an object or an array of objects are validated by the Symfony validator component, so it cascades validation.

There are only two options:

- `groups` defines to which validation groups the `Cascade` constraint belongs to, with the same behavior as any regular validator constraint. If you do not define `groups` it is set to `Default`. The `Cascade` constraint is only executed if one of the validation groups matches.

- `trigger` defines which validation groups to trigger on the child object(s). By default only `Default` is triggered, so if you want any other validation groups to trigger you have to specify them with `trigger`. The validation groups of the "parent" are never cascaded.

That is it!

Example
-------

Below is a common example in real applications: You might have an order and multiple possible addresses for the order (one for shipping, one for invoice) with different requirements, and some addresses should be optional, but if they are specified they should still be validated.

`$shippingAddress` shows how to trigger specific validation groups in the child object, in this case to make the phone number a mandatory part of the information (often the case for shipping, but usually not necessary for other uses) in addition to the "Default" constraints.

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
     */
    #[Cascade(trigger: ['Default', 'phoneNumberMandatory'])]
    public Address $shippingAddress;

    /**
     * Validate $invoiceAddress only if validation group
     * "alternateInvoiceAddress" is passed to validator
     *
     * Validates only "Default" validation group in $invoiceAddress, so phone number is optional
     */
    #[Assert\NotNull(groups: ['alternateInvoiceAddress'])]
    #[Cascade(groups: ['alternateInvoiceAddress'])]
    public ?Address $invoiceAddress = null;
}

class Address
{
    #[Assert\Length(min: 1, max: 50)]
    public string $street = '';

    #[Assert\Length(min: 1, max: 50)]
    public string $city = '';

    #[Assert\Length(min: 1, max: 50, groups: ['phoneNumberMandatory'])]
    public string $phoneNumber = '';
}

$order = new Order();
$order->shippingAddress = new Address();
$order->invoiceAddress = new Address();

// This validates with the "Default" validation group,
// so only shippingAddress must be specified
$symfonyValidator->validate($order);

// This also validates the invoice address in addition
// to the shipping address
$symfonyValidator->validate($order, null, [
    "Default",
    "alternateInvoiceAddress",
]);
```

Why not use the `Valid` constraint?
-----------------------------------

The current implementation of the `Valid` constraint in the Symfony validator component has severe limitations when it comes to validation groups and behaves differently than any other constraint:

### `Valid` constraint without validation group

```php
#[Assert\Valid]
public $someobject;
```

The above code looks like a regular assertion, but it behaves differently:

- The assertion is always executed, no matter what validation group you give to the validator
- The assertion therefore does not belong to the "Default" group

This is fine for simple objects or when you don't need any validation groups at all, but it is still different from any other assertion, as you cannot "skip" this constraint even if you later add validation groups.

### `Valid` constraint with validation group(s)

```php
#[Assert\Valid(groups: ['invoice'])]
public $someobject;
```

The `Valid` assertion above only triggers when you validate the "invoice" validation group, which is what you would expect. Yet there is plenty of unexpected behavior:

- It only triggers the validation group "invoice" in $someobject, no other validation groups are passed to the object (if, for example, you are validating the groups "Default" and "invoice" the group "Default" never reaches $someobject, only "invoice")
- There is no way to change which validation groups are triggered in $someobject
- The "traverse" option for `Valid` is not used when a validation group is defined. Although the "traverse" option should probably not be used or needed in general

Having validation groups both as a trigger and as a filter severly limits how you can use it, and makes most use cases (like our [example with addresses](#example)) impossible to do with `Valid`. Even if you manage to make it work, your code will not be self explanatory and it is easy to make mistakes or misunderstand the attributes.

`Cascade` as defined in this component separates which validation group the constraint belongs to and which validation groups are triggered in the child object(s). What it cannot do is cascade the validation groups of the parent to the child object, as this information is only available in the `RecursiveContextualValidator` class of the validator component and cannot be accessed without changing a lot of the internals of the validator component (unfortunately).