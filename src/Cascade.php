<?php

namespace Squirrel\ValidatorCascade;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Cascade extends Constraint
{
    public function __construct(
        /** @var string[] For which groups to trigger this contraint (default Symfony Validator behavior) */
        array $groups = [Constraint::DEFAULT_GROUP],
        /** @var string[] Which validation groups to trigger in any child objects */
        protected array $trigger = [Constraint::DEFAULT_GROUP]
    ) {
        parent::__construct(groups: $groups);
    }

    public function getTrigger(): array
    {
        return $this->trigger;
    }
}
