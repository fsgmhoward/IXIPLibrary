<?php
/*
 * Copyright 2016 Howard Liu
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * Cron class for IP Library
 * Dependence:
 *  - IX Logging Class
 */
namespace IXNetwork;

class IXIPLibraryCron
{
    // DO NOT edit this constant's value as it is for the class version check
    const classVersion = '2.0';
    
    private static $self;
    private $log;
    private $type;
    
    private function __construct($type, $isLog, $logFile)
    {
        $this->log = $isLog ? new Lib\IXLogging($logFile, null, array(true)) : null;
        $this->log ? $this->log->add(' [INFO] Cron Class Initiated') : null;
        $this->type = $type;
        return true;
    }

    private function __clone()
    {
    }

    public static function cron($type = 'City', $isLog = true, $logFile = null)
    {
        //Prevent the class from initiating twice
        if (self::$self instanceof self) {
            return false;
        } else {
            $logFile = $logFile ? $logFile : (__DIR__.'/../Log/Cron.log');
            self::$self = new self($type, $isLog, $logFile);
            self::$self->classVersionCheck();
            return self::$self->ipDatabaseVersionCheck();
        }
    }
    
    private function classVersionCheck()
    {
        $remoteVersion = file_get_contents('http://version.ixnet.work/IXIPLibrary.fsgmhoward.php');
        if (self::classVersion != $remoteVersion) {
            $this->log ? $this->log->add(" [INFO] Newer version is found") : null;
            $this->log ? $this->log->add("        Local version is ".self::classVersion." while remote version is $remoteVersion") : null;
            $this->log ? $this->log->add("        Please update via https://github.com/fsgmhoward/IXIPLibrary/releases") : null;
        }
        return true;
    }
    
    private function ipDatabaseVersionCheck()
    {
        if (file_exists(__DIR__."/GeoLite2-$this->type.mmdb")) {
            $localVersion = md5_file(__DIR__."/GeoLite2-$this->type.mmdb");
            $remoteVersion = file_get_contents("http://geolite.maxmind.com/download/geoip/database/GeoLite2-$this->type.md5");
            if ($localVersion != $remoteVersion) {
                $this->log ? $this->log->add(" [INFO] Version Mismatch, A Newer Version Will Be Downloaded") : null;
            } else {
                $this->log ? $this->log->add(" [INFO] GeoLite2-$this->type.mmdb Is Up-to-date") : null;
                $this->log ? $this->log->add(' [INFO] Exited') : null;
                return true;
            }
        }

        // Remove the old version database
        if (file_exists(__DIR__."/GeoLite2-$this->type.mmdb")) {
            unlink(__DIR__."/GeoLite2-$this->type.mmdb");
        }

        // Download the new database from MaxMind
        $remoteFile = "http://geolite.maxmind.com/download/geoip/database/GeoLite2-$this->type.mmdb.gz";
        $remoteFile = file_get_contents($remoteFile);
        if ($remoteFile) {
            $localFile = fopen(__DIR__."/GeoLite2-$this->type.mmdb.gz", 'wb');
            fwrite($localFile, $remoteFile);
            fclose($localFile);
            self::ungz(__DIR__."/GeoLite2-$this->type.mmdb.gz");
            unlink(__DIR__."/GeoLite2-$this->type.mmdb.gz");
            $this->log ? $this->log->add(" [INFO] Download Finished, A Newer Version Has Been Extracted") : null;
            $this->log ? $this->log->add(' [INFO] Exited') : null;
            return true;
        } else {
            $this->log ? $this->log->add('[ERROR] Download Error') : null;
            $this->log ? $this->log->add(' [INFO] Exited') : null;
            return false;
        }
    }
    
    private static function ungz($file_name)
    {
        // Original Code From http://stackoverflow.com/questions/3293121/how-can-i-unzip-a-gz-file-with-php

        // Raising this value may increase performance
        $buffer_size = 65536; // read 64kb at a time
        $out_file_name = str_replace('.gz', '', $file_name);

        // Open our files (in binary mode)
        $file = gzopen($file_name, 'rb');
        $out_file = fopen($out_file_name, 'wb');

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }

        // Files are done, close files
        fclose($out_file);
        gzclose($file);
    }
}
