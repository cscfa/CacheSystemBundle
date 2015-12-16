<?php
/**
 * This file is a part of CSCFA project.
 * 
 * PHP version 5.5
 * 
 * @category Provider
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
namespace Cscfa\Bundle\CacheSystemBundle\Object\provider;

use Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Cscfa\Bundle\CacheSystemBundle\Object\Element\CacheCollection;
use Symfony\Component\Serializer\Exception\MappingException;
use Cscfa\Bundle\CacheSystemBundle\Object\DirectoryException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * FileSystemCache provider
 *
 * This provider allow to manage
 * the cache on filesystem
 *
 * @category Provider
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
class FileSystemCache implements CacheProviderInterface
{

    /**
     * Cache directory
     * 
     * The filesystem directory
     * to use to manage cache
     * 
     * @var string
     */
    protected $cacheDir;

    /**
     * Prefix
     * 
     * The filesystem cache 
     * directory prefix to 
     * use to manage cache
     * 
     * @var string
     */
    protected $prefix;
    
    /**
     * Timestamp
     * 
     * The cache max
     * timestamp range
     * 
     * @var integer
     */
    protected $timestamp;

    /**
     * Set kerrnel
     * 
     * Set the application
     * kernel
     * 
     * @param Kernel $kernel - the application kernel
     * 
     * @return CacheProviderInterface
     *
     * @see \Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface::setKernel()
     */
    public function setKernel(Kernel $kernel)
    {
        $this->cacheDir = $kernel->getCacheDir() . '/';
        
        return $this;
    }

    /**
     * Set prefix
     * 
     * Set the cache prefix
     * 
     * @param string $prefix - the prefix
     * 
     * @return CacheProviderInterface
     * 
     * @see \Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface::setPrefix()
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        
        return $this;
    }
    
    /**
     * Set timestamp
     * 
     * Set the cache max
     * timestamp range
     * 
     * @param integer $timestamp - the timestamp range
     * 
     * @return CacheProviderInterface
     * 
     * @see \Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface::setTimestamp()
     */
    public function setTimestamp($timestamp){
        $this->timestamp = $timestamp;
        
        return $this;
    }

    /**
     * Get
     * 
     * Return a cache
     * element
     * 
     * @param string $id - the cache element name
     * 
     * @return CacheCollection - the CacheCollection element
     * 
     * @see \Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface::get()
     */
    public function get($id)
    {
        if ($this->accessible($id)) {
            $collection = new CacheCollection();
            $collection->setTimestampOffset($this->timestamp);
            $collection->unserialize(file_get_contents($this->getPath($id)));
            
            if ($collection->getId() !== $id) {
                throw new MappingException(sprintf("The file %s does not containt the right cache element", $this->getPath($id)), 500);
            } else {
                return $collection;
            }
        } else {
            if (! $this->exist($id)) {
                return null;
            } else {
                
                $read = $this->readable($id);
                $write = $this->writable($id);
                
                if ($read && ! $write) {
                    throw new AccessDeniedException(sprintf("The file %s is on read only state", $this->getPath($id)), 403);
                } else if (! $read && ! $write) {
                    throw new AccessDeniedException(sprintf("The file %s cannot be read or write", $this->getPath($id)), 403);
                } else {
                    throw new AccessDeniedException(sprintf("The file %s cannot be read", $this->getPath($id)), 403);
                }
            }
        }
    }

    /**
     * Save
     * 
     * Save a cache collection
     * 
     * @param CacheCollection $collection - the cache collection to save
     * 
     * @return CacheProviderInterface
     * 
     * @see \Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface::save()
     */
    public function save(CacheCollection $collection)
    {
        $directory = $this->cacheDir . $this->prefix;
        $dir = is_dir($directory);
        $read = is_readable($directory);
        $write = is_writable($directory);
        
        if ($dir && $read && $write) {
            if ($this->exist($collection->getId()) && ! $this->accessible($collection->getId())) {
                
                $read = $this->readable($collection->getId());
                $write = $this->writable($collection->getId());
                
                if ($read && ! $write) {
                    throw new AccessDeniedException(sprintf("The file %s is on read only state", $this->getPath($collection->getId())), 403);
                } else if (! $read && ! $write) {
                    throw new AccessDeniedException(sprintf("The file %s cannot be read or write", $this->getPath($collection->getId())), 403);
                }
            }
            
            $collection->setTimestampOffset($this->timestamp);
            $data = $collection->serialize();
            $result = file_put_contents($this->getPath($collection->getId()), $data);
            
            if ($result === false) {
                throw new FileException(sprintf("A failure occured during writing file %s", $this->getPath($collection->getId())), 500);
            } else if ($result !== strlen($data)) {
                throw new FileException(sprintf("An error occured during writing file %s. Writed %d bytes, expect %d", $this->getPath($collection->getId()), $result, strlen($data)), 500);
            }
        } else {
            if (! $dir) {
                
                if (! mkdir($directory, 0775, true)) {
                    throw new DirectoryException(sprintf("The %s directory does not exist", $directory), 500);
                } else {
                    
                    if (!chmod($directory, 0775)) {
                        throw new DirectoryException(sprintf("The %s directory present permission error", $directory), 500);
                    }
                    
                    $this->save($collection);
                }
            } else if ($read && ! $write) {
                throw new AccessDeniedException(sprintf("The directory %s is on read only state", $directory), 403);
            } else if (! $read && ! $write) {
                throw new AccessDeniedException(sprintf("The directory %s cannot be read or write", $directory), 403);
            } else {
                throw new AccessDeniedException(sprintf("The directory %s cannot be read", $directory), 403);
            }
        }
    }

    /**
     * Accessible
     * 
     * Check if a cache id is
     * accessible
     * 
     * @param string $id - the cache id
     * 
     * @return boolean - the accessibility state
     */
    protected function accessible($id)
    {
        return $this->exist($id) && $this->readable($id) && $this->writable($id);
    }

    /**
     * Writable
     * 
     * Check if a cache id is
     * writable
     * 
     * @param string $id - the cache id
     * 
     * @return boolean - the writable state
     */
    protected function writable($id)
    {
        return is_writable($this->getPath($id));
    }

    /**
     * Readable
     * 
     * Check if a cache id is
     * readable
     * 
     * @param string $id - the cache id
     * 
     * @return boolean - the readable state
     */
    protected function readable($id)
    {
        return is_readable($this->getPath($id));
    }

    /**
     * Exist
     * 
     * Check if a given cache
     * id exist
     * 
     * @param string $id - the cache id
     * 
     * @return boolean - the existance state
     */
    protected function exist($id)
    {
        return is_file($this->getPath($id));
    }

    /**
     * Get path
     * 
     * Get the path to a
     * cache id
     * 
     * @param string $id - the cache id
     * 
     * @return string - the path
     */
    protected function getPath($id)
    {
        return $this->cacheDir . $this->prefix . '/' . $id . '.ser';
    }
}
