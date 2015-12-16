<?php
/**
 * This file is a part of CSCFA cache system project.
 * 
 * The cache system project is a symfony bundle written in php
 * with Symfony2 framework.
 * 
 * PHP version 5.5
 * 
 * @category Bundle
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @filesource
 * @link     http://cscfa.fr
 */

namespace Cscfa\Bundle\CacheSystemBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @category Bundle
 * @package  CscfaCacheSystemBundle
 * @author   Matthieu VALLANCE <matthieu.vallance@cscfa.fr>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://cscfa.fr
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     * 
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('cscfa_cache_system');
        
        $rootNode
                ->children()
                ->scalarNode('provider')->defaultValue("Cscfa\Bundle\CacheSystemBundle\Object\provider\FileSystemCache")->end()
                ->end()
                ->children()
                ->scalarNode('prefix')->defaultNull()->end()
                ->end()
                ->children()
                ->scalarNode('timestamp')->defaultNull()->end()
                ->end();

        return $treeBuilder;
    }
}
