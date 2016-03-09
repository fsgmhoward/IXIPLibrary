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
 * Json class for IP Library
 * Dependence:
 *  - IX Logging Class
 *  - Composer Vendor
 */

namespace IXNetwork;

use GeoIp2\Database\Reader;
use IXNetwork\Lib\IXLogging;

class IXIPLibraryJson
{
    private static $log;
    private static $reader;
    private static $lastType = null;

    public static function getJson($ip, $type = 'City', $isLogging = true, $logFile = null)
    {
        if ((! self::$log instanceof IXLogging) && $isLogging) {
            $logFile = $logFile ? $logFile : __DIR__.'/../Log/Json.log';
            self::$log = new Lib\IXLogging($logFile, null, array(true), true);
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
            if (! (file_exists(__DIR__."/../GeoLite2-$type.mmdb"))) {
                if ($isLogging) {
                    self::$log->add('[ERROR] Database Not Found. Please Run cron.php to download the latest database.');
                    self::$log->terminate();
                }
                return false;
            }
            self::$reader = new Reader(__DIR__."/../GeoLite2-$type.mmdb");
            self::$lastType = $type;
        }

        // Some codes refer to https://github.com/maxmind/GeoIP2-php/blob/master/README.md
        if ($type = 'City') {
            $record = self::$reader->city($ip);
            $json['state'] = $record->mostSpecificSubdivision->isoCode;
            $json['city'] = $record->city->name;
            $json['postcode'] = $record->postal->code;
            $json['latitude'] = $record->location->latitude;
            $json['longitude'] = $record->location->longitude;
        } else {
            $record = self::$reader->country($ip);
        }
        $json['country'] = $record->country->isoCode;
        $json['database'] = 'GeoIP2Lite Database by Maxmind - http://www.maxmind.com';

        if ($isLogging) {
            self::$log->add(' [INFO] Query Has Been Successfully Handled');
            self::$log->terminate();
        }

        return $json;
    }
}
