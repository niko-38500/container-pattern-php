<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassWithDependencyAndParameter
{
    public function __construct(public ClassWithDependencies $classWithDependencies, public string $boundParameter)
    {
    }
}