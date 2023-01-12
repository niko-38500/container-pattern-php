<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassWithDeepDependencies
{
    public function __construct(public ClassWithDependencies $classWithDependencies)
    {
    }
}