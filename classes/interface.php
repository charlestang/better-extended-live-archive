<?php

/**
 * The interface of Cache
 * 
 * @method void set(string $key, mixed $value) put some data into cache
 * @method mixed get(string $key) retrieve data from cache, if the data dose not exist, false will be returned
 * @method void del(string $key) clear data with the specific key in cache
 */
interface BelaCache {
    public function set($key, $value);
    public function get($key);
    public function del($key);

    public function clearAllCache();
}
