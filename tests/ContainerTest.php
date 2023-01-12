<?php

namespace Container\Tests;

use Container\Container;
use Container\ContainerInterface;
use Container\Exception\Container\DuplicateException;
use Container\Exception\Container\NotFoundException;
use Container\Tests\Tools\DataFixtures\ClassWithDependencies;
use Container\Tests\Tools\DataFixtures\ClassWithNoDependencies;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = Container::getContainer();
        $this->container->reset();
    }

    public function testGet()
    {
        $this->container->set(ClassWithNoDependencies::class);

        self::assertInstanceOf(
            ClassWithNoDependencies::class,
            $this->container->get(ClassWithNoDependencies::class)
        );
    }

    public function testSingleton()
    {
        self::assertSame($this->container, Container::getContainer());
    }

    public function testGetWithError()
    {
        $this->expectException(NotFoundException::class);
        $this->container->get('bad class name');
    }

    public function testSetWithDuplicate()
    {
        $this->container->set(ClassWithNoDependencies::class);
        $this->expectException(DuplicateException::class);
        $this->expectErrorMessage(sprintf(
            'The service %s is already registered',
            ClassWithNoDependencies::class
        ));
        $this->container->set(ClassWithNoDependencies::class);
    }

    public function testSetWithSubDependencies()
    {
        $expectedClass = new ClassWithDependencies(new ClassWithNoDependencies());
        $this->container->set(ClassWithDependencies::class);

        self::assertInstanceOf(ClassWithDependencies::class, $expectedClass);
        self::assertInstanceOf(ClassWithNoDependencies::class, $expectedClass->classWithNoDependencies);
    }

    public function testHas()
    {
        $this->container->set(ClassWithNoDependencies::class);
        self::assertTrue($this->container->has(ClassWithNoDependencies::class));
    }

    public function testHasWithNoMatching()
    {
        self::assertFalse($this->container->has(ClassWithNoDependencies::class));
    }

    public function testSetParameter()
    {
        $this->container->setParameter('key', 'value');
        self::assertSame('value', $this->container->getParameter('key'));
    }

    public function testSetParameterWithDuplicate()
    {
        $this->container->setParameter('key', 'value');
        $this->expectException(DuplicateException::class);
        $this->expectExceptionMessage('Parameter key is already registered');
        $this->container->setParameter('key', 'value');
    }

    public function testGetParameterWithBadKey()
    {
        $this->expectException(NotFoundException::class);
        $this->expectErrorMessage('Parameter bad_key does not exist or is not registered');
        $this->container->getParameter('bad_key');
    }

    public function testHasParameter()
    {
        $this->container->setParameter('key', 'value');
        self::assertTrue($this->container->hasParameter('key'));
    }

    public function testHasParameterWithNoMatching()
    {
        self::assertFalse($this->container->hasParameter('key'));
    }
}
