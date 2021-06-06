<?php
namespace LifeStyleCoding\Container;

use Error;
use ReflectionClass;

// TODO //
// handle routing with comment block

class Container {
    
    // TODO //
    protected array $classList = [];
    // list of instancied class to be recalled

    public function resolve(string $class) {

        $parameters = [];
        $reflection = new ReflectionClass($class);

        if ($constructor = $reflection->getConstructor()) {
            foreach($constructor->getParameters() as $param) {
                if ($param->hasType() && !$param->getType()->isBuiltin()) {
                    $className = $param->getType()->getName();
                    try {
                        array_push($parameters, new $className);
                    } catch(Error) {
                        array_push($parameters, $this->resolve($className));
                    }
                }
            }
            $obj = $reflection->newInstanceArgs($parameters);
            return $obj;
        }

        $obj = $reflection->newInstance();
        return $obj;
    }

    public function execute(Object $object, string $action) {

        $parameters = [];
        $instance = new ReflectionClass($object);

        foreach($instance->getMethod($action)->getParameters() as $param) {
            if ($param->hasType() && !$param->getType()->isBuiltin()) {
                $className = $param->getType()->getName();
                try {
                    array_push($parameters, new $className);
                } catch(Error) {
                    array_push($parameters, $this->resolve($className));
                }
            }
        }
        $action = $action;
        $object->$action(...$parameters);
    }
}