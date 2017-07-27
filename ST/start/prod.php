<?php

// Register the special elasticache handler here
/*Cache::extend('elasticache', function() {
  require_once(__DIR__.'/../libraries/ElasticacheConnector.php');
	$servers = Config::get('cache.memcached');
	$elasticache = new Illuminate\Cache\ElasticacheConnector();

	$memcached = $elasticache->connect($servers);

	return new Illuminate\Cache\Repository(new Illuminate\Cache\MemcachedStore($memcached, Config::get('cache.prefix')));
});
*/