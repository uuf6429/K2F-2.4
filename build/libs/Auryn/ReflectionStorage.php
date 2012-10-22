<?php

namespace Auryn;

use ReflectionMethod,
    ReflectionParameter;

interface ReflectionStorage {
    
    /**
     * Retrieves ReflectionClass instances, caching them for future retrieval
     * 
     * @param string $class
     * @return \ReflectionClass
     */
    function getClass($class);
    
    /**
     * Retrieves and caches the constructor (ReflectionMethod) for the specified class
     * 
     * @param string $class
     * @return \ReflectionMethod
     */
    function getConstructor($class);
    
    /**
     * Retrieves and caches an array of constructor parameters for the given class
     * 
     * @param string $class
     * @return array[ReflectionParameter]
     */
    function getConstructorParameters($class);
    
    /**
     * Retrieves the class type-hint from a given ReflectionParameter
     * 
     * There is no way to directly access a parameter's type-hint without
     * instantiating a new ReflectionClass instance and calling its getName()
     * method. This method stores the results of this approach so that if
     * the same parameter type-hint or ReflectionClass is needed again we
     * already have it cached.
     * 
     * @param ReflectionMethod $method
     * @param ReflectionParameter $parameter
     */
    function getParamTypeHint(ReflectionMethod $method, ReflectionParameter $parameter);
}
