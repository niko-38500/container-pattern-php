<?php
namespace LifeStyleCoding\Container;

use ReflectionClass;

class Container {
    
    protected string $class;
    protected string|null $action;
    // TODO //
    // liste des class instancier pour les re appeler

    public function __construct(string $class, string|null $action = null) {
        $this->class = $class;
        $this->action = $action;
    }

    public function resolveToString() {

    }

    public function resolve() {

        $parameters = [];
        $reflection = new ReflectionClass($this->class);

        if ($constructor = $reflection->getConstructor()) {
            foreach($constructor->getParameters() as $param) {
                if ($param->hasType() && !$param->getType()->isBuiltin()) {
                    $class = $param->getType()->getName();
                    // TODO //
                    // re appeler resolve si classe a besoin de construire un nouvelle obj
                    array_push($parameters, new $class);
                }
            }
            $this->newObj = $reflection->newInstanceArgs($parameters);
            return $this->newObj;
        }

        $className = $reflection->newInstance();
        $this->newObj = $className;
        return $this->newObj;
    }

    public function execute(Object $object) {

        // TODO //
        // $id = 10;
        // $pattern = "/@Route\(\"\/[a-zA-Z0-9\_\-]*\/\{([a-z]*)\}\"\)/";
        // $test = $instance->getMethod($this->action)->getDocComment();
        // preg_match($pattern, $test, $match);
        // $result = $match[1];
        // var_dump(filter_input(INPUT_SERVER, "PATH_INFO") ?? "/");

        $parameters = [];
        $instance = new ReflectionClass($object);

        foreach($instance->getMethod($this->action)->getParameters() as $param) {
            if ($param->hasType() && !$param->getType()->isBuiltin()) {
                $className = $param->getType()->getName();
                // TODO //
                // re appeler resolve si classe a besoin de construire un nouvelle obj
                array_push($parameters, new $className);
            }
        }
        $action = $this->action;
        $object->$action(...$parameters);

        // foreach($instance->getMethods() as $method) {
        //     foreach($method->getParameters() as $param) {
        //     }
        // }
    }
}