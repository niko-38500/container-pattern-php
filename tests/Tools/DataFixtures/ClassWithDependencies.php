<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassWithDependencies
{
    private ClassWithNoDependencies $classWithNoDependencies;

    public function __construct(ClassWithNoDependencies $classWithNoDependencies)
    {
        $this->classWithNoDependencies = $classWithNoDependencies;
    }
}