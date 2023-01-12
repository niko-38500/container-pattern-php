<?php

namespace Container\Tests\Resolver;

use Container\Container;
use Container\Exception\AutowireException;
use Container\Exception\Resolver\UndefinedClassException;
use Container\Resolver\Resolver;
use Container\Tests\Tools\DataFixtures\ClassWithDeepDependencies;
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
        Container::getContainer()->reset();
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
        self::assertInstanceOf(ClassWithNoDependencies::class, $resolvedClass->classWithNoDependencies);
    }

    public function testResolveClassWithDeepDependencies()
    {
        $resolvedClass = $this->resolver->autowire(ClassWithDeepDependencies::class);
        self::assertInstanceOf(ClassWithDeepDependencies::class, $resolvedClass);
    }

    public function testResolveClassWithParameterAndDeepDependencies()
    {
        $container = Container::getContainer();
        $value = 'value';
        $container->setParameter('boundParameter', $value);
        $resolvedClass = $this->resolver->autowire(ClassWithDependencyAndParameter::class);
        self::assertInstanceOf(ClassWithDependencyAndParameter::class, $resolvedClass);
        self::assertInstanceOf(ClassWithDependencies::class, $resolvedClass->classWithDependencies);
        self::assertInstanceOf(
            ClassWithNoDependencies::class,
            $resolvedClass->classWithDependencies->classWithNoDependencies
        );
        self::assertEquals($value, $resolvedClass->boundParameter);
    }

    public function testResolveClassWithUnregisteredParameter()
    {
        $this->expectException(AutowireException::class);
        $this->expectExceptionMessage(
            'Can not autowire the parameter $boundParameter for the class ' .
            'Container\Tests\Tools\DataFixtures\ClassWithDependencyAndParameter'
        );
        $this->resolver->autowire(ClassWithDependencyAndParameter::class);
    }
}
