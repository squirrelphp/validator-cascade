<?php

namespace Squirrel\ValidatorCascade\Examples;

use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    #[Assert\Length(min: 1, max: 50)]
    public string $street = '';

    #[Assert\Length(min: 1, max: 50)]
    public string $city = '';

    #[Assert\Length(min: 1, max: 50, groups: ['phoneNumberMandatory'])]
    public string $phoneNumber = '';
}
