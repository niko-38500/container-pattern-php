<?php
namespace Container;

use Container\Exception\AutowireException;
use Container\Exception\Container\DuplicateException;
use Container\Exception\Container\NotFoundException;
use Container\Exception\Resolver\UndefinedClassException;
use Container\Resolver\Resolver;

class Container implements ContainerInterface
{
    private static ?self $instance = null;

    /** @var array<string, object> */
    private array $services = [];
    /** @var array<string, string> */
    private array $parameters = [];

    private function __construct() {}

    public static function getContainer(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get a service from the container
     *
     * @throws NotFoundException
     */
    public function get(string $service): object
    {
        if (!$this->has($service)) {
            throw new NotFoundException(sprintf('Service %s does not exist or is not registered', $service));
        }

        return $this->services[$service];
    }

    /**
     * Define a service into the container
     *
     * @throws DuplicateException
     * @throws AutowireException
     * @throws UndefinedClassException
     * @throws NotFoundException
     */
    public function set(string $service): void
    {
        if ($this->has($service)) {
            throw new DuplicateException(sprintf('The service %s is already registered', $service));
        }

        $resolver = new Resolver();
        $resolvedClass = $resolver->autowire($service);

        $this->services[$service] = $resolvedClass;
    }

    /**
     * check if container has specified service
     */
    public function has(string $service): bool
    {
        return array_key_exists($service, $this->services);
    }

    /**
     * Get a parameter from the container
     *
     * @throws NotFoundException
     */
    public function getParameter(string $parameter): string
    {
        if (!$this->hasParameter($parameter)) {
            throw new NotFoundException(sprintf('Parameter %s does not exist or is not registered', $parameter));
        }

        return $this->parameters[$parameter];
    }

    /**
     * Set a parameter into the container
     *
     * @throws DuplicateException
     */
    public function setParameter(string $key, string $value): void
    {
        if ($this->hasParameter($key)) {
            throw new DuplicateException(sprintf('Parameter %s is already registered', $key));
        }

        $this->parameters[$key] = $value;
    }

    /**
     * Check if container has parameter
     */
    public function hasParameter(string $parameter): bool
    {
        return array_key_exists($parameter, $this->parameters);
    }

    /**
     * reset the container from all services and parameters
     */
    public function reset(): void
    {
        $this->services = [];
        $this->parameters = [];
    }
}