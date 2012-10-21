<?php

namespace Auryn;

use ReflectionClass,
    ReflectionMethod,
    ReflectionParameter;

class ReflectionPool implements ReflectionStorage {
    
    const CACHE_KEY_CLASSES = 'auryn\\refls\\classes';
    const CACHE_KEY_CTORS = 'auryn\\refls\\ctors';
    const CACHE_KEY_CTOR_PARAMS = 'auryn\\refls\\ctor-params';
    
    /**
     * @var array
     */
    protected $cache = array();
    
    /**
     * Retrieves ReflectionClass objects, caching them for future retrievals
     * 
     * @param string $class The class we want to reflect
     * @throws \ReflectionException If the class can't be found or auto-loaded
     * @return \ReflectionClass
     */
    public function getClass($class) {
        $lowClass = strtolower($class);
        $cacheKey = self::CACHE_KEY_CLASSES . '\\' . $lowClass;
        
        $reflectionClass = $this->fetchFromCache($cacheKey);
        if (!$reflectionClass) {
            $reflectionClass = new ReflectionClass($class);
            $this->storeInCache($cacheKey, $reflectionClass);
        }
        
        return $reflectionClass;
    }
    
    protected function fetchFromCache($key) {
        return array_key_exists($key, $this->cache) ? $this->cache[$key] : false;
    }
    
    protected function storeInCache($key, $data) {
        $this->cache[$key] = $data;
    }
    
    /**
     * Retrieves and caches the class's constructor ReflectionMethod
     * 
     * @param string $class The class whose constructor we want to reflect
     * @return \ReflectionMethod Returns reflected constructor or NULL if class has no constructor.
     */
    public function getConstructor($class) {
        $lowClass = strtolower($class);
        $cacheKey = self::CACHE_KEY_CTORS . '\\' . $lowClass;
        
        $reflectedCtor = $this->fetchFromCache($cacheKey);
        
        if (false === $reflectedCtor) {
            $reflectionClass = $this->getClass($class);
            $reflectedCtor = $reflectionClass->getConstructor();
            $this->storeInCache($cacheKey, $reflectedCtor);
        }
        
        return $reflectedCtor;
    }
    
    /**
     * Retrieves and caches constructor parameters for the given class name
     *
     * @param string $class The class whose constructor parameters we're retrieving
     * @return array An array of ReflectionParameter objects or NULL if no constructor exists
     */
    public function getConstructorParameters($class) {
        $lowClass = strtolower($class);
        $cacheKey = self::CACHE_KEY_CTOR_PARAMS . '\\' . $lowClass;
        
        $reflectedCtorParams = $this->fetchFromCache($cacheKey);
        
        if (false !== $reflectedCtorParams) {
            return $reflectedCtorParams;
        } elseif ($reflectedCtor = $this->getConstructor($class)) {
            $reflectedCtorParams = $reflectedCtor->getParameters();
        } else {
            $reflectedCtorParams = NULL;
        }
        
        $this->storeInCache($cacheKey, $reflectedCtorParams);
        
        return $reflectedCtorParams;
    }
    
    /**
     * Retrieves the class type-hint from a given ReflectionParameter
     * 
     * There is no way to retrieve the string type-hint value directly from a ReflectionParameter
     * instance -- a new ReflectionClass must be generated from the type-hint and its name returned.
     * We require the ReflectionMethod parameter so that a unique cache key can be generated for
     * future type-hint retrieval.
     * 
     * @param ReflectionFunctionAbstract $function
     * @param ReflectionParameter $param
     * @return string The type-hint of the specified parameter or NULL if none exists
     */
    public function getParamTypeHint(ReflectionMethod $method, ReflectionParameter $param) {
        $lowClass = strtolower($method->class);
        $lowMethod = strtolower($method->name);
        $lowParam = strtolower($param->name);
        
        $paramCacheKey = self::CACHE_KEY_CLASSES . "\\$lowMethod\\param-$lowParam";
        $typeHint = $this->fetchFromCache($paramCacheKey);
        
        if (false !== $typeHint) {
            return $typeHint;
        }
        
        if ($reflectionClass = $param->getClass()) {
            $typeHint = $reflectionClass->getName();
            $classCacheKey = self::CACHE_KEY_CLASSES . '\\' . strtolower($typeHint);
            $this->storeInCache($classCacheKey, $reflectionClass);
        } else {
            $typeHint = NULL;
        }
        
        $this->storeInCache($paramCacheKey, $typeHint);
        
        return $typeHint;
    }
}
