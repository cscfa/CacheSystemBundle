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
namespace Cscfa\Bundle\CacheSystemBundle\Object\Element;

/**
 * CacheCollection
 *
 * The CacheCollection manage a
 * cache element set
 *
 * @category Object
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
class CacheCollection implements \Serializable
{

    /**
     * Elements
     * 
     * The elements array
     * 
     * @var array
     */
    protected $elements;

    /**
     * Id
     * 
     * The cache id
     * 
     * @var string
     */
    protected $id;
    
    /**
     * Timestamp offset
     * 
     * The cache timestamp
     * offset
     * 
     * @var integer
     */
    protected $timestampOffset;

    /**
     * Constructor
     * 
     * Default constructor
     * 
     * @param string $id - the current cache id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * Get id
     * 
     * Return the current 
     * cache id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get
     * 
     * Get an element
     * 
     * @param string $key - the element key
     * 
     * @return CacheElement - the element
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->elements[$key];
        } else {
            return null;
        }
    }

    /**
     * Create
     * 
     * Create an element. Note
     * this method do not override
     * an existing element.
     * 
     * @param string $key     - the element key
     * @param string $content - the element content
     * 
     * @return CacheCollection - the current instance
     */
    public function create($key, $content)
    {
        if (! $this->has($key)) {
            $this->elements[$key] = new CacheElement($key);
            $this->elements[$key]->setContent($content);
        }
        return $this;
    }

    /**
     * Has
     * 
     * Check if the element
     * exist
     * 
     * @param string $key - the element key
     */
    public function has($key)
    {
        return isset($this->elements[$key]);
    }

    /**
     * Remove
     * 
     * Remove an element
     * 
     * @param string $key - the element key to remove
     * 
     * @return CacheCollection - the current instance
     */
    public function rem($key)
    {
        if ($this->has($key)) {
            unset($this->elements[$key]);
        }
        
        return $this;
    }

    /**
     * Get timestamp offset
     * 
     * Return the cache timestamp
     * offset
     * 
     * @return number
     */
    public function getTimestampOffset()
    {
        return $this->timestampOffset;
    }

    /**
     * Set timestamp offset
     * 
     * Set the cache timestamp
     * offset
     * 
     * @param integer $timestampOffset - the timestamp offset
     * 
     * @return CacheCollection - the current instance
     */
    public function setTimestampOffset($timestampOffset)
    {
        $this->timestampOffset = $timestampOffset;
        return $this;
    }
 
    /**
     * Serialize
     * 
     * Serialize the current
     * instance
     * 
     * @see Serializable::serialize()
     */
    public function serialize()
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($dateTime->getTimestamp() + $this->timestampOffset);
        
        foreach ($this->elements as $element) {
            if ($element instanceof CacheElement) {
                $element->setTimeOver($dateTime);
            }
        }
        
        return serialize(array(
            "id" => $this->id,
            "elements" => $this->elements
        ));
    }

    /**
     * Unserialize
     * 
     * Unserialize an
     * instance
     * 
     * @see Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        $tmp = unserialize($serialized);
        
        $this->id = $tmp['id'];
        
        $dateTime = new \DateTime();
        
        foreach ($tmp['elements'] as $element) {
            if ($element instanceof CacheElement){
                if ($element->getTimeOver() >= $dateTime) {
                    $this->elements[$element->getKey()] = $element;
                }
            }
        }
    }
}
