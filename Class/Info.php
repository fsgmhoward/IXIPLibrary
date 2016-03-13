<?php
/**
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

/**
 * Json class for IP Library
 */

namespace IXNetwork\IPLib;

use GeoIp2\Database\Reader;
use IXNetwork\Lib\Logging;

class Info
{
    private static $log;
    private static $reader;
    private static $lastType = null;

    public static function getInfo($ip, $type = 'City', $isLogging = true, $logFile = null)
    {
        if ((! self::$log instanceof Logging) && $isLogging) {
            $logFile = $logFile ? $logFile : 'Log/Info.log';
            self::$log = new Logging($logFile, null, array(true), true);
            self::$log->add(' [INFO] Json Log File Initiated');
        }

        if (! in_array($type, ['City', 'Country'])) {
            if ($isLogging) {
                self::$log->add('[ERROR] Parameter Error: Type can only be \'Country\' or \'City\'');
                self::$log->terminate();
            }
            return false;
        }

        if ($type != self::$lastType) {
            if (! (file_exists("GeoLite2-$type.mmdb"))) {
                if ($isLogging) {
                    self::$log->add('[ERROR] Database Not Found. Please Run cron.php to download the latest database.');
                    self::$log->terminate();
                }
                return false;
            }
            self::$reader = new Reader("GeoLite2-$type.mmdb");
            self::$lastType = $type;
        }

        // Some codes refer to https://github.com/maxmind/GeoIP2-php/blob/master/README.md
        if ($type = 'City') {
            $record = self::$reader->city($ip);
            $info['state'] = $record->mostSpecificSubdivision->isoCode;
            $info['city'] = $record->city->name;
            $info['postcode'] = $record->postal->code;
            $info['latitude'] = $record->location->latitude;
            $info['longitude'] = $record->location->longitude;
        } else {
            $record = self::$reader->country($ip);
        }
        $info['country'] = $record->country->isoCode;
        $info['database'] = 'GeoIP2Lite Database by Maxmind - http://www.maxmind.com';
        $info['raw'] = $record;

        if ($isLogging) {
            self::$log->add(' [INFO] Query Has Been Successfully Handled');
            self::$log->terminate();
        }

        return $info;
    }
}
