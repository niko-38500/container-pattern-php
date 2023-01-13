<?php

namespace Container\Tests\Resolver;

use Container\Container;
use Container\Exception\AutowireException;
use Container\Exception\Resolver\CircularReferenceException;
use Container\Exception\Resolver\UndefinedClassException;
use Container\Resolver\Resolver;
use Container\Tests\Tools\DataFixtures\ClassWithCircularReference;
use Container\Tests\Tools\DataFixtures\ClassWithDeepDependencies;
use Container\Tests\Tools\DataFixtures\ClassWithDependencies;
use Container\Tests\Tools\DataFixtures\ClassWithDependencyAndParameter;
use Container\Tests\Tools\DataFixtures\ClassWithIndirectCircularReference;
use Container\Tests\Tools\DataFixtures\ClassWithNoDependencies;
use Container\Tests\Tools\DataFixtures\OtherClassWithCircularReference;
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

    public function testResolveByFQCNWithNotExisting(): void
    {
        $this->expectException(UndefinedClassException::class);
        $this->resolver->autowire('expect to lead to an exception');
    }

    public function testResolveClass(): void
    {
        $resolvedClass = $this->resolver->autowire(ClassWithNoDependencies::class);
        self::assertIsObject($resolvedClass);
        self::assertInstanceOf(ClassWithNoDependencies::class, $resolvedClass);
    }

    public function testResolveClassWithDependencies(): void
    {
        $resolvedClass = $this->resolver->autowire(ClassWithDependencies::class);
        self::assertInstanceOf(ClassWithDependencies::class, $resolvedClass);
        self::assertInstanceOf(ClassWithNoDependencies::class, $resolvedClass->classWithNoDependencies);
    }

    public function testResolveClassWithDeepDependencies(): void
    {
        $resolvedClass = $this->resolver->autowire(ClassWithDeepDependencies::class);
        self::assertInstanceOf(ClassWithDeepDependencies::class, $resolvedClass);
    }

    public function testResolveClassWithParameterAndDeepDependencies(): void
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

    public function testResolveClassWithUnregisteredParameter(): void
    {
        $this->expectException(AutowireException::class);
        $this->expectExceptionMessage(
            'Can not autowire the parameter $boundParameter for the class ' .
            'Container\Tests\Tools\DataFixtures\ClassWithDependencyAndParameter'
        );
        $this->resolver->autowire(ClassWithDependencyAndParameter::class);
    }

    public function testResolveWithCircularReference(): void
    {
        self::expectException(CircularReferenceException::class);
        self::expectExceptionMessage(sprintf(
            'A circular reference has been detected into the class %s for the dependency %s',
            ClassWithCircularReference::class,
            OtherClassWithCircularReference::class
        ));

        $this->resolver->autowire(ClassWithCircularReference::class);
    }

    public function testResolveWithIndirectCircularReference(): void
    {
        self::expectException(CircularReferenceException::class);
        self::expectExceptionMessage(sprintf(
            'A circular reference has been detected into the class %s for the dependency %s',
            ClassWithCircularReference::class,
            OtherClassWithCircularReference::class
        ));

        $this->resolver->autowire(ClassWithIndirectCircularReference::class);
    }
}
