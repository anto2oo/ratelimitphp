<?php
class RateLimiter
{
	private $Memcache;

	function __construct($MEMCACHE_SERVER_ADDR = "127.0.0.1") {
		if (!class_exists('Memcache')) {
			throw new Exception('Memcache PHP module is required and cannot be found');
		} else {
			$this->Memcache = new Memcache();
		}

		if(!$this->Memcache->connect($MEMCACHE_SERVER_ADDR)){
			throw new Exception('Could not connect to Memcache server on ' . $MEMCACHE_SERVER_ADDR);
		}
	}


	function hit($IDENTIFIER, $LIMIT, $EXPIRATION){

		if($this->Memcache->get($IDENTIFIER)){
			$this->Memcache->increment($IDENTIFIER, 1);
		} else {
			$this->Memcache->set($IDENTIFIER, 1, false, time() + $EXPIRATION);
		}

		$HITS = $this->Memcache->get($IDENTIFIER);
		if($HITS >= $LIMIT){
			$GRANTED = False;
		} else {
			$GRANTED = True;
		}

		return(array("granted" => $GRANTED, "limit" => $LIMIT, "limit_left" => $LIMIT - $HITS));
	}

}
