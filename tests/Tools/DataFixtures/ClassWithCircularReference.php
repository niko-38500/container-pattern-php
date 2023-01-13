<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class ClassWithCircularReference
{
    public function __construct(public OtherClassWithCircularReference $otherClassWithCircularReference)
    {
    }
}