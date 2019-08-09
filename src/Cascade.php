<?php

namespace Squirrel\ValidatorCascade;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Cascade extends Constraint
{
    /**
     * @var mixed Which validation groups to trigger in any child objects, can be array or string
     */
    public $trigger = [Constraint::DEFAULT_GROUP];
}
