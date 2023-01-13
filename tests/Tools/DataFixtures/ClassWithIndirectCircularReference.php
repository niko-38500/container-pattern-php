<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassWithIndirectCircularReference
{
    public function __construct(
        public ClassWithDeepDependencies $classWithDeepDependencies,
        public ClassWithCircularReference $classWithCircularReference
    ) {
    }
}