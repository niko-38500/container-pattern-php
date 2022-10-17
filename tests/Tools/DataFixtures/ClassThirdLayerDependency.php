<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassThirdLayerDependency
{
    private ClassWithDependencies $classWithDependencies;

    public function __construct(ClassWithDependencies $classWithDependencies)
    {
        $this->classWithDependencies = $classWithDependencies;
    }
}