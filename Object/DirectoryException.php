<?php
/**
 * This file is a part of CSCFA cache system project.
 * 
 * The cache system project is a symfony bundle written in php
 * with Symfony2 framework.
 * 
 * PHP version 5.5
 * 
 * @category Exception
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */

namespace Cscfa\Bundle\CacheSystemBundle\Object;

/**
 * DirectoryException exception
 * 
 * The DirectoryException exception indicate
 * a directory error
 * 
 * @category Exception
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */
class DirectoryException extends \Exception
{

    /**
     * Constructor
     * 
     * Default constructor
     * 
     * @param string     $message  - the message [optional]
     * @param integer    $code     - the error code [optional]
     * @param \Exception $previous - the previous exception [optional]
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}