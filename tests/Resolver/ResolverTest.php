<?php

namespace Container\Tests\Resolver;

use Container\Container;
use Container\Exception\AutowireException;
use Container\Exception\Resolver\UndefinedClassException;
use Container\Resolver\Resolver;
use Container\Tests\Tools\DataFixtures\ClassThirdLayerDependency;
use Container\Tests\Tools\DataFixtures\ClassWithDependencies;
use Container\Tests\Tools\DataFixtures\ClassWithDependencyAndParameter;
use Container\Tests\Tools\DataFixtures\ClassWithNoDependencies;
use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    public Resolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new Resolver();
    }

    public function testResolveByFQCNWithNotExisting()
    {
        $this->expectException(UndefinedClassException::class);
        $this->resolver->autowire('expect to lead to an exception');
    }

    public function testResolveClass()
    {
        $resolvedClass = $this->resolver->autowire(ClassWithNoDependencies::class);
        self::assertIsObject($resolvedClass);
        self::assertInstanceOf(ClassWithNoDependencies::class, $resolvedClass);
    }

    public function testResolveClassWithDependencies()
    {
        $resolvedClass = $this->resolver->autowire(ClassWithDependencies::class);
        self::assertInstanceOf(ClassWithDependencies::class, $resolvedClass);
    }

    public function testResolveClassWithDeepDependencies()
    {
        $resolvedClass = $this->resolver->autowire(ClassThirdLayerDependency::class);
        self::assertInstanceOf(ClassThirdLayerDependency::class, $resolvedClass);
    }

    public function testResolveClassWithParameterAndDeepDependencies()
    {
        $container = Container::getContainer();
        $container->setParameter('boundParameter', 'value');
        $resolvedClass = $this->resolver->autowire(ClassWithDependencyAndParameter::class);
        self::assertInstanceOf(ClassThirdLayerDependency::class, $resolvedClass);
    }

    public function testResolveClassWithUnresolvableParameter()
    {
        $this->expectException(AutowireException::class);
        $this->expectExceptionMessage(
            'Can not autowire the parameter $boundParameter for the class ' .
            'Container\Tests\Tools\DataFixtures\ClassWithDependencyAndParameter'
        );
        $this->resolver->autowire(ClassWithDependencyAndParameter::class);
    }
}
