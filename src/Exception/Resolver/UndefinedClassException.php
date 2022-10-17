<?php

declare(strict_types=1);

namespace Container\Exception\Resolver;

class UndefinedClassException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}