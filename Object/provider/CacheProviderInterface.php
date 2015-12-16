<?php
/**
 * This file is a part of CSCFA project.
 * 
 * PHP version 5.5
 * 
 * @category Interface
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
namespace Cscfa\Bundle\CacheSystemBundle\Object\provider;

use Symfony\Component\HttpKernel\Kernel;
use Cscfa\Bundle\CacheSystemBundle\Object\Element\CacheCollection;

/**
 * CacheProviderInterface interface
 *
 * This interface define the default
 * cache provider methods
 * 
 * @category Interface
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
interface CacheProviderInterface
{
    /**
     * Set kerrnel
     * 
     * Set the application
     * kernel
     * 
     * @param Kernel $kernel - the application kernel
     * 
     * @return CacheProviderInterface
     */
    public function setKernel(Kernel $kernel);
    
    /**
     * Set prefix
     * 
     * Set the cache prefix
     * 
     * @param string $prefix - the prefix
     * 
     * @return CacheProviderInterface
     */
    public function setPrefix($prefix);
    
    /**
     * Set timestamp
     * 
     * Set the cache max
     * timestamp range
     * 
     * @param integer $timestamp - the timestamp range
     * 
     * @return CacheProviderInterface
     */
    public function setTimestamp($timestamp);
    
    /**
     * Get
     * 
     * Return a cache
     * element
     * 
     * @param string $id - the cache element name
     * 
     * @return CacheCollection | null
     */
    public function get($id);
    
    /**
     * Save
     * 
     * Save a cache collection
     * 
     * @param CacheCollection $collection - the cache collection to save
     * 
     * @return CacheProviderInterface
     */
    public function save(CacheCollection $collection);
}
