<?php

declare(strict_types=1);

namespace Container\Resolver;

use Container\Container;
use Container\Exception\AutowireException;
use Container\Exception\Container\NotFoundException;
use Container\Exception\Resolver\UndefinedClassException;

class Resolver
{
    private Container $container;

    public function __construct()
    {
        $this->container = Container::getContainer();
    } // TODO import cache machine to save dependencies into xml file

    /**
     * @throws UndefinedClassException|NotFoundException
     * @throws AutowireException
     */
    public function autowire(string $className): object
    {
        if (!class_exists($className)) {
            throw new UndefinedClassException(sprintf('The class %s does not exists', $className));
        }

        $reflectionClass = new \ReflectionClass($className);
        $classConstructor = $reflectionClass->getConstructor();

        if (!$classConstructor) {
            return new $className();
        }

        $constructorParameters = [];

        foreach ($classConstructor->getParameters() as $parameter) {
            if (!$parameter->hasType()) {
                throw new AutowireException(sprintf(
                    'Can not autowire the parameter $%s for the class %s',
                    $parameter->getName(),
                    $className
                ));
            }

            $parameterType = $parameter->getType();
            if (!$parameterType->isBuiltin()) {
                $constructorParameters[] = $this->autowire($parameterType->getName());
                continue;
            }

            $parsedParameter = $this->parseParameterName($parameterType->getName(), $parameter->getName());

            if ($this->container->hasParameter($parsedParameter)) {
                $constructorParameters[] = $this->container->getParameter($parsedParameter);
            }
        }

        return new $className(...$constructorParameters);
    }

    private function parseParameterName(string $type, string $name): string
    {
        return $type . ' $' . $name;
    }

    /**
     * @throws NotFoundException|AutowireException
     */
    private function resolveParameter(string $parameter): string
    {
        return $this->container->getBoundParameter($parameter);
    }
}