<?php
/*
 * Copyright (c) 2016 Howard Liu
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
/*
 * This is one of the file of IX Network Public Library
 * Published Under MIT License
 * Author's blog: https://blog.ixnet.work
 * Version 1.0
 */
class log{
    private $logFile;
    private $token = false;
    private $ip = false;
    private $timeFormat = 'Y/m/d H:i:s e'; //Format for date(): This one is like '2016/01/03 13:28:32 UTC'.

    public function __construct($logFile = './general.log', $timeFormat = NULL, $isToken = false, $isIP = false, $isIPLengthFixed = true){
        $logFileX = stripos($logFile, '/') ? explode('/',$logFile) : explode('\\',$logFile);
        $j = '';
        $k = 0;
        while($k<(sizeof($logFileX)-1)){
            $j .= $logFile[$k]."/";
            if(!file_exists($j)) mkdir($j);
            $k++;
        }
        $this->logFile = fopen($logFile, 'at');
        if($isToken) $this->token = $this->generateRandomCode();
        if($isIP) $this->ip = $this->getIP($isIPLengthFixed);
        if($timeFormat) $this->timeFormat = $timeFormat;
        $this->add('Logging Class Initiated');
    }

    public function add($message, $type = 'INFO'){
        $write = '['.date($this->timeFormat).']';
        if ($this->ip) $write .= '['.$this->ip.']';
        if ($this->token) $write.= '['.$this->token.']';
        return fwrite($this->logFile, $write."[$type]$message\n");
    }

    //Generate a ramdom code to seperate different sessions
    private function generateRandomCode(){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $code ='';
        for($i=0;$i<=16;$i++){
            $code .= $chars[rand(0,strlen($chars)-1)];
        }
        return md5($code);
    }

    //Get Remote User's IP
    private function getIP($isIPLengthFixed){
        if (getenv("HTTP_X_FORWARDED_FOR"))
        {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        elseif (getenv("HTTP_CLIENT_IP"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        elseif (getenv("REMOTE_ADDR"))
        {
            $ip = getenv("REMOTE_ADDR");
        }
        else
        {
            $ip = false;
        }
        if($ip && $isIPLengthFixed){
            $ip = explode(',', $ip);
            $newIP = explode('.', $ip[0]);
            $ip = '';
            foreach($newIP as $i){
                $ip .= sprintf("%03d", $i);
            }
        }
        return $ip;
    }

    public function __destruct()
    {
        fclose($this->logFile);
    }
}