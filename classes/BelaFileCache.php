<?php

/**
 * Cache implementation with file api.
 */
class BelaFileCache implements BelaCache {

    public $cacheFilePath = null;

    /**
     * Remove the data of specific key.
     * 
     * @param string $key
     */
    public function del($key) {
        $this->delFile($key);
    }

    /**
     * Retrieve back the data from cache by key.
     * 
     * @param string $key
     * @return mixed return the data on success, or FALSE on data dose not exist.
     */
    public function get($key) {
        $value = $this->readFile($key);

        if (empty($value)) {
            return false;
        }

        return unserialize($value);
    }

    /**
     * Save data in cache.
     * 
     * @param string $key
     * @param mixed $value
     * @return void no returned value
     */
    public function set($key, $value) {
        $this->writeFile($key, serialize($value));
    }
    

    private function writeFile($fileName, $content) {
        if (empty($content)) {
            return;
        }

        $this->checkPath();

        $fileName = $this->cacheFilePath . DIRECTORY_SEPARATOR . $fileName;

        if (false === ($fd = fopen($fileName, 'w'))) {
            throw new BelaIoException('Create file ' . $fileName . ' failed!');
        }

        if (!@fwrite($fd, $content)) {
            throw new BelaIoException('Write cache file failed. File Name: ' . $fileName . "\n File Contents: " . $content);
        }

        fclose($fd);
    }

    private function checkPath() {
        if (!is_dir($this->cacheFilePath) && !mkdir($this->cacheFilePath)) {
            throw new BelaIoException('Cache file path does not exist, and cannot be created.');
        }

        if (!is_writable($this->cacheFilePath)) {
            throw new BelaIoException('Cache file path is not writable.');
        }
    }

    private function readFile($fileName) {
        $this->checkPath();

        $fileName = $this->cacheFilePath . DIRECTORY_SEPARATOR . $fileName;

        if (is_readable($fileName)) {
            $fileContents = file_get_contents($fileName);

            if (false === $fileContents) {
                throw new BelaIoException('Cache file read failed. File Name: ' . $fileName);
            }

            return $fileContents;
        }

        if (file_exists($fileName)) {
            throw new BelaIoException('Cache file exists but cannot be read. File Name: ' . $fileName);
        }

        return '';
    }

    private function delFile($fileName) {
        $this->checkPath();

        $fileName = $this->cacheFilePath . DIRECTORY_SEPARATOR . $fileName;

        if (!unlink($fileName)) {
            throw new BelaIoException('Cache file remove failed.');
        }
    }

    /**
     * @todo this method should be optimized.
     * @return boolean
     */
    public function clearAllCache() {
        $this->checkPath();
		$del_cache_path = $this->cacheFilePath . DIRECTORY_SEPARATOR . "*.dat";
		if ( ($filelist=glob($del_cache_path)) === false ) {
            return false;
        }
		foreach ($filelist as $filename) {
			if (!@unlink($filename)) return false;	// delete it
		}
		return true;
        
    }

}
