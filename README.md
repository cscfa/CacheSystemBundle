# CacheSystemBundle
### Version 1.0.1-dev

The cscfa caching system tool allow to store informations files into the application cache directory and automatically manage the out of date values.

#####Installation

Register the bundle into app/appKernel.php

```
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            [...]
            new Cscfa\Bundle\CacheSystemBundle\CscfaCacheSystemBundle(),
        );
        
        [...]
    }
}
```

#####Use manager

The cache manager allow to process a cache storage by using a closure. It will automatically use cache if exist, or create it with the closure result.

```
// in controller

	return new Response($this->get("cscfa_cache_system_manager")->process("id@key", function($controller, $name){
		return $controller->renderView("AcmeDemoBundle:Default:index.html.twig", array("name"=>$name)); 
	}, $this, $name));
	
```

#####Getting cache

The main cache system is registered as a service into the 'cscfa_cache_system_cache' id. It's used to get the cache provider.

The provider returned by the cache object implements the CacheProviderInterface and is intanciated with the configuration 'cscfa_cache_system.provider'
parameter. Note that a filesystem provider is set by default.

```
// in controller

	//Cscfa\Bundle\CacheSystemBundle\Object\Cache
	$cache = $this->get("cscfa_cache_system_cache");
	
	//Cscfa\Bundle\CacheSystemBundle\Object\provider\CacheProviderInterface
	$provider = $cache->getProvider();
```

#####Use the cache

The cache use a cache id to select a collection of cache elements. Each elements can be selected by their keys. This elements 
have a content and an out of date element.

The deletion of the elements on out of time case is done automatically during the retreiving of the cache collection.

```
	//Cscfa\Bundle\CacheSystemBundle\Object\Element\CacheCollection
	$collection = $provider->get("cacheId");
	
	// create a cache key with a content :
	$collection->create("20151216", "cache key creation");
	
	if ($collection->has("20151216")) {
		//Cscfa\Bundle\CacheSystemBundle\Object\Element\CacheElement
		$element = $collection->get("20151216");
	}
	
	// persist cache collection
	$provider->save($collection);
	
	/* 
	 * note the element is soft removed as long as 'save' is not called
	 * and it will be returned at the next get('id') request 
	 * (while the out of time is not reached)
	 */ 
	$collection->rem("20151216");
```

#####Configure the bundle

The config file can be write as the follow :

```
// app/config

cscfa_cache_system:
	provider:  'provider complete class (Cscfa\Bundle\CacheSystemBundle\Object\provider\FileSystemCache as default)'
	prefix:    'string (null as default)'
	timestamp: integer (null as default)
```

The 'provider' indicate the provider class to use. Everyone class implementing CacheProviderInterface can be used.

The 'prefix' indicate a cache prefix. The usage of this depend of the provider. By example, the FileSystemCache use it to define a specific repository into the cache directory.

The 'timestamp' indicate the number of second before unvalidate the cache element.
