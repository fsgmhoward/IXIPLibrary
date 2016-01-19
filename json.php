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
 * Author's blog: https://blog.ixnet.work
 * Version 1.0
 * API Library is from: https://github.com/maxmind/GeoIP2-php
 */
include './log.class.php';
$log = new log('./json.log', NULL, true, true);

if(!isset($_GET['ip'])){
    $log->add('Failed Attempt! Missing Parameter');
    exit('Missing Parameter');
}

//Set type, City or Country
$type = 'City';
$log->add("Started A Session for $type Query. IP is ".$_GET['ip']);
//Some codes refer to https://github.com/maxmind/GeoIP2-php/blob/master/README.md
include "./vendor/autoload.php";
use GeoIp2\Database\Reader;

$reader = new Reader("./GeoLite2-$type.mmdb");

if($type = 'City'){
    $record = $reader->city($_GET['ip']);
}else{
    $record = $reader->country($_GET['ip']);
}

$json = array();

$json['country'] = $record->country->isoCode;
if($type = 'City'){
    $json['state'] = $record->mostSpecificSubdivision->isoCode;
    $json['city'] = $record->city->name;
    $json['postcode'] = $record->postal->code;
    $json['latitude'] = $record->location->latitude;
    $json['longitude'] = $record->location->longitude;
}
$json['database'] = 'GeoIP2Lite Database by Maxmind - http://www.maxmind.com';

header('content-type: application/json');
echo json_encode($json);
$log->add('Finished Query. Session Terminated');