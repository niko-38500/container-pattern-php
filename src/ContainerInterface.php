<?php

declare(strict_types=1);

namespace Container;

interface ContainerInterface
{
    public function get(string $service): object;

    public function set(string $service): void;

    public function has(string $service): bool;

    public function getParameter(string $parameter): string;

    public function setParameter(string $key, string $value): void;

    public function hasParameter(string $parameter): bool;

    public function bindParameter(string $namespace, string $key, string $value): void;

    public function getBoundParameter(string $key, string $classFQCN): string;

    public function hasBoundParameter(string $key, string $classFQCN): bool;

    public function reset(): void;
}