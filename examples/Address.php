<?php

namespace Squirrel\ValidatorCascade\Examples;

use Symfony\Component\Validator\Constraints as Assert;

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
    public $street;

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
    public $city;

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
    #[Assert\NotNull(groups: ['phoneNumberMandatory'])]
    #[Assert\NotBlank(groups: ['phoneNumberMandatory'])]
    #[Assert\Length(min: 1, max: 50)]
    public $phoneNumber;
}
