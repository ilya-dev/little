<?php

class Little {

    /**
     * The container's bindings
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The container's shared instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Resolve the given type from the container
     *
     * @param  string $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        if (isset($this->instances[$abstract]))
        {
            return $this->instances[$abstract];
        }

        $instance = $this->build($this->getConcrete($abstract));

        if ($this->isShared($abstract))
        {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Bind a type into the container
     *
     * @param  string          $abstract
     * @param  string|Closure  $concrete
     * @param  boolean         $shared
     * @return void
     */
    public function bind($abstract, $concrete, $shared = false)
    {
        unset($this->instances[$abstract]);

        if ( ! $concrete instanceof Closure)
        {
            $concrete = function($container) use($concrete)
            {
                return $container->make($concrete);
            };
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * Bind a shared type into the container
     *
     * @param  string         $abstract
     * @param  string|Closure $concrete
     * @return void
     */
    public function singleton($abstract, $concrete)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Bind an existing instance into the container
     *
     * @param  string $abstract
     * @param  mixed  $instance
     * @return void
     */
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Determine if the given abstract type has been bound
     *
     * @param  string  $abstract
     * @return boolean
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract])
            or isset($this->instances[$abstract]);
    }

    /**
     * Determine whether a given abstract type is shared
     *
     * @param  string  $abstract
     * @return boolean
     */
    protected function isShared($abstract)
    {
        return isset($this->bindings[$abstract])
           and ($this->bindings[$abstract]['shared'] === true);
    }

    /**
     * Get the concrete type for a given abstract type
     *
     * @param  string $abstract
     * @return mixed
     */
    protected function getConcrete($abstract)
    {
        if (isset($this->bindings[$abstract]))
        {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * Instantiate a concrete instance of the given type
     *
     * @param  string $concrete
     * @return mixed
     */
    protected function build($concrete)
    {
        if ($concrete instanceof Closure)
        {
            return $concrete($this);
        }

        try
        {
            $reflector = new ReflectionClass($concrete);
        }
        catch(ReflectionException $exception)
        {
            throw new LittleException($exception->getMessage());
        }

        if ( ! $reflector->isInstantiable())
        {
            throw new LittleException("$concrete is not instantiable");
        }

        if (is_null($constructor = $reflector->getConstructor()))
        {
            return new $concrete;
        }

        $parameters = $constructor->getParameters();

        return $reflector->newInstanceArgs($this->getDependencies($parameters));
    }

    /**
     * Resolve all of the given dependencies
     *
     * @param  array $parameters
     * @return array
     */
    protected function getDependencies(array $parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter)
        {
            // $parameter is an instance of \ReflectionParameter
            $dependency = $parameter->getClass();

            if ($dependency instanceof ReflectionClass)
            {
                $dependencies[] = $this->make($dependency->name);

                continue;
            }

            $dependencies[] = $this->resolvePrimitive($parameter);
        }

        return $dependencies;
    }

    /**
     * Resolve a dependency of a primitive type
     *
     * @param  ReflectionParameter $parameter
     * @return mixed
     */
    protected function resolvePrimitive(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable())
        {
            return $parameter->getDefaultValue();
        }

        throw new LittleException("Unresolvable dependency $parameter");
    }

}

class LittleException extends \Exception {  }
