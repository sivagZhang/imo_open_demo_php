<?php

class Cache
{
    private static function getCache()
    {
        static $Cache;
        if (!is_object($Cache)) {
            $Cache = new FileCache(DIR_DATA . "filecache.php");
        }
        return $Cache;
    }
    
    public static function get($key)
    {
        return self::getCache()->get($key);
    }
    
    public static function set($key, $value, $duration = 7000)
    {
        self::getCache()->set($key, $value, $duration);
    }
}

class FileCache
{
    private $filename;
    public function __construct($fname) {
        $this->filename = $fname; 
    }

    function get($key)
    {
        if(empty($key))
            return flase;
        
        $cachefile = $this->get_file($this->filename); 
        $data = json_decode($cachefile, true);
        //Log::i("cacheKey:" . $key);
        //Log::i("cachefile:" . $cachefile);
 
        if(!$data || !array_key_exists($key, $data))
            return false;
        
        $item = $data["$key"];
        if(!$item)
            return false;
       
        if($item['expire_time'] > 0 && time() > $item['expire_time'] )
        {
            Log::i("key ". $key . "expires! currtime:" . time());
            return false;
        }
        
        return $item["$key"];
    }
    
    function set($key, $value, $duration)
    {
        if($key&&$value){
            $data = json_decode($this->get_file($this->filename), true);
            
            $item = array();
            $item["$key"] = $value;

            if(is_numeric($duration) && $duration > 0){
                $item['expire_time'] = time() + $duration;
            }
            else{
                $item['expire_time'] = 0;
            }
            $item['create_time'] = time();
            
            $data["$key"] = $item;
            $this->set_file($this->filename, json_encode($data));
        }
    }

    function get_file($filename) 
    {
        if (!file_exists($filename)) {
            $fp = fopen($filename, "w");
            fwrite($fp, "<?php exit();?>" . '');
            fclose($fp);
            return false;
        }else{
            $content = trim(substr(file_get_contents($filename), 15));
        }
        return $content;
    }

    function set_file($filename, $content) 
    {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }
}
