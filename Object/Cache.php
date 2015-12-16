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
namespace Cscfa\Bundle\CacheSystemBundle\Object;

use Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Cache object
 * 
 * This object manage the cache
 * system
 * 
 * @category Object
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
class Cache
{
    /**
     * Provider
     * 
     * The cache provider
     * 
     * @var CacheProviderInterface
     */
    protected $provider;
    
    /**
     * Set arguments
     * 
     * Initialyze the cache object
     * 
     * @param string $providerClass - the provider class
     */
    public function setArguments(Kernel $kernel, $config){
        
        $provider = $config['provider'];
        $prefix = $config['prefix'];
        $timestamp = $config['timestamp'];
        
        $this->setProvider(new $provider());
        $this->provider->setKernel($kernel);
        $this->provider->setPrefix($prefix);
        $this->provider->setTimestamp($timestamp);
    }

    /**
     * Get provider
     * 
     * Return the cache provider
     * 
     * @return CacheProviderInterface - the cache provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set provider
     * 
     * Set the cache provider
     * 
     * @param CacheProviderInterface $provider - the cache provider
     * 
     * @return Cache - the current instance
     */
    protected function setProvider(CacheProviderInterface $provider)
    {
        $this->provider = $provider;
        return $this;
    }   
}
