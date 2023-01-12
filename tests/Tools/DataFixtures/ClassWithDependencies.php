<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassWithDependencies
{
    public function __construct(public ClassWithNoDependencies $classWithNoDependencies)
    {
    }
}