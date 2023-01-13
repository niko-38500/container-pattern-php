<?php

declare(strict_types=1);

namespace Container\Tests\Tools\DataFixtures;

class OtherClassWithCircularReference
{
    public function __construct(public ClassWithCircularReference $circularReference)
    {
    }
}