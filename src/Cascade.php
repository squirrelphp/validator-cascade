<?php

namespace Squirrel\ValidatorCascade;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class Cascade extends Constraint
{
    /**
     * @var array Which validation groups to trigger in any child objects
     */
    protected array $trigger = [Constraint::DEFAULT_GROUP];

    /**
     * @param array|null $options
     * @param string[] $groups
     * @param string[] $trigger
     */
    public function __construct(
        $options = null,
        array $groups = [Constraint::DEFAULT_GROUP],
        array $trigger = [Constraint::DEFAULT_GROUP]
    ) {
        if (isset($options['groups'])) {
            $groups = $options['groups'];
        }

        if (isset($options['trigger'])) {
            $trigger = $options['trigger'];
        }

        $this->groups = $groups;
        $this->trigger = $trigger;
    }

    public function getTrigger(): array
    {
        return $this->trigger;
    }
}
