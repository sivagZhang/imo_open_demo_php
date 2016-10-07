<?php

class Log
{
    public static function i($msg)
    {
        self::write('INFO ', $msg);
    }
    
    public static function e($msg)
    {
        self::write('ERROR', $msg);
    }

    private static function write($logLevel, $msg)
    {
        $fileName = DIR_DATA . "isv.log";
        $logFile = fopen($fileName, "aw");
        fwrite($logFile, date("Y-m-d H:i:s") . " " . $logLevel . " " . $msg . "\n");
        fclose($logFile);
    }
}
