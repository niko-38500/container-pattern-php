<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassWithNonTypedParameter
{
    public function __construct($nonTypedArgs)
    {
    }
}