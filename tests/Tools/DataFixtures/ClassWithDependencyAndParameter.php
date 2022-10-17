<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassWithDependencyAndParameter
{
    private ClassWithDependencies $classWithDependencies;
    private string $boundParameter;

    public function __construct(ClassWithDependencies $classWithDependencies, string $boundParameter)
    {
        $this->classWithDependencies = $classWithDependencies;
        $this->boundParameter = $boundParameter;
    }
}