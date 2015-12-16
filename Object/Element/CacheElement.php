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
 * CacheElement
 *
 * The CacheElement manage the
 * cache element
 *
 * @category Object
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
class CacheElement implements \Serializable
{

    /**
     * Key
     * 
     * The element key
     * 
     * @var string
     */
    protected $key;

    /**
     * Time over
     * 
     * The date time to
     * ensure the cache 
     * element
     * 
     * @var \DateTime
     */
    protected $timeOver;

    /**
     * Content
     * 
     * The cache content
     * 
     * @var string
     */
    protected $content;

    /**
     * Constructor
     * 
     * Default constructor
     * 
     * @param string $key - the element key
     */
    public function __construct($key = null)
    {
        $this->key = $key;
    }

    /**
     * Get key
     * 
     * Get the element key
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get time over
     * 
     * Get the element time over
     * 
     * @return DateTime
     */
    public function getTimeOver()
    {
        return $this->timeOver;
    }

    /**
     * Set time over
     * 
     * Set the element time over
     * 
     * @param \DateTime $timeOver - the element time over
     * 
     * @return CacheElement
     */
    public function setTimeOver(\DateTime $timeOver)
    {
        $this->timeOver = $timeOver;
        return $this;
    }

    /**
     * Get content
     * 
     * Get the element content
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     * 
     * Set the element content
     * 
     * @param string $content - the element content
     * 
     * @return CacheElement
     */
    public function setContent($content)
    {
        $this->content = $content;
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
        return serialize(
            array(
                "key" => $this->key,
                "timeOver" => $this->timeOver->getTimestamp(),
                "content" => $this->content
            )
        );
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
        
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($tmp['timeOver']);
        
        $this->key = $tmp['key'];
        $this->timeOver = $dateTime;
        $this->content = $tmp['content'];
    }
}
