<?php
/*
 * Logging Class
 * Written by Howard Liu <howard@ixnet.work>
 * Licensed under MIT
 * Version 1.0 β
 */
namespace IXNetwork\Lib;

class IXLogging
{
    private $logFile;
    private $token = false;
    public $ip = false;
    private $timeFormat = 'Y/m/d H:i:s e'; //Format of date()

    /**
     * ixLogging constructor.
     *
     * @param string     $logFile
     * @param string     $timeFormat
     * @param bool|array $token
     * @param bool       $isIP
     * @param bool       $isIPLengthFixed
     */
    public function __construct(
        $logFile = './general.log',
        $timeFormat = null,
        $token = array(false),
        $isIP = false,
        $isIPLengthFixed = true
    ) {
    
        //Replace the '/' with '\' when running under Windows OS
        $logFileX = stripos($logFile, '/') ? explode('/', $logFile) : explode('\\', $logFile);

        //Create the respective folder
        $j = '';
        $k = 0;
        while ($k<(sizeof($logFileX)-1)) {
            $j .= $logFile[$k]."/";
            if (!file_exists($j)) {
                mkdir($j);
            }
            $k++;
        }
        $this->logFile = fopen($logFile, 'at');

        /**
         * Array token
         * $token[1] bool, means weather token is enabled
         * $token[2] int,  means the length of random codes for md5 sum
         * $token[3] int,  means the length of the token
         */
        if ($token[0]) {
            if (isset($token[1]) && is_int($token[1])) {
                if (isset($token[2])&& is_int($token[2])) {
                    $this->token = $this->generateRandomCode($token[1], $token[2]);
                } else {
                    $this->token = $this->generateRandomCode($token[1]);
                }
            } else {
                $this->token = $this->generateRandomCode();
            }
        }
        if ($isIP) {
            $this->ip = $this->getIP($isIPLengthFixed);
        }
        if ($timeFormat) {
            $this->timeFormat = $timeFormat;
        }
        $this->add('Logging Class Initiated');
    }

    /**
     * Print a message
     *
     * @param  $message
     * @return int
     */
    public function add($message)
    {
        $write = '['.date($this->timeFormat).']';
        if ($this->ip) {
            $write .= '['.$this->ip.']';
        }
        if ($this->token) {
            $write.= '['.$this->token.']';
        }
        return fwrite($this->logFile, $write.$message."\n");
    }

    /**
     * Generate a random code to separate different sessions
     *
     * @param  int $codeLength
     * @param  int $returnLength
     * @return string
     */
    private function generateRandomCode($codeLength = 16, $returnLength = 6)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $code ='';
        for ($i=0; $i<=$codeLength; $i++) {
            $code .= $chars[rand(0, strlen($chars)-1)];
        }
        return substr(md5($code), 0, $returnLength);
    }

    /**
     * Get Remote User's IP
     *
     * @param  $isIPLengthFixed
     * @return bool|string
     */
    private function getIP($isIPLengthFixed)
    {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "Unknown";
        }

        // Fix the length of IP for a nicer log
        // E.g. 100.3.14.45 --> 100.003.014.045
        if ($isIPLengthFixed) {
            if ($ip == "Unknown") {
                $ip = "****Unknown****";
            } else {
                $ip = explode(',', $ip);
                $newIP = explode('.', $ip[0]);
                $ip = '';
                foreach ($newIP as $i) {
                    $ip .= sprintf("%03d", $i).".";
                }
            }
            $ip = substr($ip, 0, 15);
        }
        return $ip;
    }
}
