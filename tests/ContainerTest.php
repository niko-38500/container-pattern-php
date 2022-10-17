<?php

namespace Container\Tests;

use Container\Container;
use Container\ContainerInterface;
use Container\Exception\AutowireException;
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
        $this->container->set(ClassWithNoDependencies::class);
        $this->container->setParameter('param', 'value');
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

    public function testBindParameter()
    {
        $this->container->bindParameter('_default', 'string $test', 'value');
        self::assertSame(
            'value',
            $this->container->getBoundParameter('string $test', ClassWithNoDependencies::class)
        );
    }

    private function parameterProvider(): \Iterator
    {
        yield 'check with valid parameter key' => ['string $test', false];
        yield 'check with invalid parameter key: no dollars signe' => ['string test', true];
        yield 'check with invalid parameter key: too mush work' => ['string $test deux', true];
        yield 'check with invalid parameter key: bad space' => ['string $ test', true];
        yield 'check with invalid parameter key: no type' => ['$test', true];
        yield 'check with invalid parameter key: just a string' => ['test', true];
    }

    /**
     * @dataProvider parameterProvider
     */
    public function testBindParameterWithInvalidKey(string $parameter, bool $shouldThrowException)
    {
        if ($shouldThrowException) {
            $this->expectException(AutowireException::class);
            $this->expectExceptionMessage('Can not bind parameter "' . $parameter . '": invalid key');
            $this->container->bindParameter('_default', $parameter, 'value');
            return;
        }
        $this->container->bindParameter(ClassWithNoDependencies::class, $parameter, 'value');
        self::assertSame(
            'value',
            $this->container->getBoundParameter($parameter, ClassWithNoDependencies::class)
        );
    }

    public function testGetBoundParameterInWrongNamespace()
    {
        $this->container->bindParameter(ClassWithDependencies::class, 'string $test', 'value');
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Parameter string $test in class ' .
            ClassWithNoDependencies::class .
            ' is not registered or in other namespace'
        );
        $this->container->getBoundParameter('string $test', ClassWithNoDependencies::class);
    }

    public function testBindParameterWithDuplicate()
    {
        $this->expectException(DuplicateException::class);
        $this->expectExceptionMessage('Error parameter "string $test" is already registered');
        $this->container->bindParameter('_default', 'string $test', 'value');
    }

    public function testGetBoundParameterWithInvalidKey()
    {
        $this->expectException(DuplicateException::class);
        $this->expectExceptionMessage('Can not get parameter "string test": invalid syntax');
        $this->container->bindParameter('_default', 'string $test', 'value');
    }

    public function testGetNotBoundParameter()
    {
        $this->expectException(AutowireException::class);
        $this->expectExceptionMessage('Parameter "string test" is not registered');
        $this->container->bindParameter('_default', 'string $test', 'value');
    }
}
