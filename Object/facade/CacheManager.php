<?php
/**
 * This file is a part of CSCFA project.
 * 
 * PHP version 5.5
 * 
 * @category Object
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
namespace Cscfa\Bundle\CacheSystemBundle\Object\facade;

use Cscfa\Bundle\CacheSystemBundle\Object\Cache;
use Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface;
use Cscfa\Bundle\CacheSystemBundle\Object\Element\CacheCollection;

/**
 * CacheManager
 *
 * The CacheManager manage
 * the cache system
 *
 * @category Object
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
class CacheManager
{

    /**
     * Cache
     * 
     * The cache object
     * 
     * @var Cache
     */
    protected $cache;

    /**
     * Provider
     * 
     * The cache provider
     * 
     * @var CacheProviderInterface
     */
    protected $provider;

    /**
     * Get cache
     * 
     * Return the cache
     * 
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set cache
     * 
     * Set the cache
     * 
     * @param Cache $cache - the cache
     * 
     * @return CacheManager
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }
 

    /**
     * Process
     * 
     * Return a cache element
     * or the result of the
     * closure if undefined
     * and register it into
     * the cache
     * 
     * @param string   $index        - the cache index formatted as 'id@key'
     * @param \Closure $closure      - the closure
     * @param mixed    $arguments... - the closure arguments
     * 
     * @return string
     * @throws \Exception - throw an exception if the result of the closure is not a string
     */
    public function process($index, \Closure $closure)
    {
        $closureArguments = func_get_args();
        $closureArguments = array_slice($closureArguments, 2);
        
        list ($id, $key) = explode("@", $index, 2);
        
        $cacheCollection = $this->getProvider()->get($id);
        if ($cacheCollection !== null) {
            if ($cacheCollection->has($key)) {
                return $cacheCollection->get($key)->getContent();
            } else {
                return $this->callClosure($closure, $closureArguments, $id, $key);
            }
        } else {
            return $this->callClosure($closure, $closureArguments, $id, $key);
        }
    }

    /**
     * Call closure
     * 
     * Call the given closure
     * and register the result
     * in cache
     * 
     * @param \Closure $closure          - the closure
     * @param array    $closureArguments - the closure arguments
     * @param string   $id               - the cache id
     * @param string   $key              - the cache key
     * 
     * @return string - the result of the closure
     * @throws \Exception - throw an exception if the result of the closure is not a string
     */
    protected function callClosure(\Closure $closure, $closureArguments, $id, $key)
    {
        $result = call_user_func_array($closure, $closureArguments);
        
        $cacheCollection = $this->getProvider()->get($id);
        if ($cacheCollection === null) {
            $cacheCollection = new CacheCollection($id);
        }
        
        if ($cacheCollection->has($key)) {
            $cacheCollection->get($key)->setContent($result);
        } else {
            $cacheCollection->create($key, $result);
        }
        
        try{
            $this->getProvider()->save($cacheCollection);
        } catch (\Exception $e) {
            throw new \Exception(sprintf("The closure must return a string value to be stored into the cache. %s returned", gettype($result)), 500);
        }
        
        return $result;
    }

    /**
     * Get provider
     * 
     * Return the provider
     * 
     * @return CacheProviderInterface
     */
    protected function getProvider()
    {
        if ($this->provider !== null) {
            return $this->provider;
        } else {
            $this->provider = $this->cache->getProvider();
            return $this->provider;
        }
    }
}