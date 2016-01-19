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
 * Recommended time interval to run this script is once a day.
 */
$version = '1.0'; //Current Version
$type = 'City'; //Only 'City' or 'Country'
include './log.class.php';
$log = new log('./cron.log');
$log->add("Cron Started");

//Version Check
if($version != file_get_contents('http://version.ixnet.work/ip_json.program.php'))
    $log->add("Newer version is found! Local version is $version while remote version is ".file_get_contents('http://version.ixnet.work/ip_json.program.php')."! Please update via https://github.com/fsgmhoward/ip_json/releases");
function ungz($fileName){
    //Original Code From http://stackoverflow.com/questions/3293121/how-can-i-unzip-a-gz-file-with-php
    // This input should be from somewhere else, hard-coded in this example
    $file_name = $fileName;

    // Raising this value may increase performance
    $buffer_size = 4096; // read 4kb at a time
    $out_file_name = str_replace('.gz', '', $file_name);

    // Open our files (in binary mode)
    $file = gzopen($file_name, 'rb');
    $out_file = fopen($out_file_name, 'wb');

    // Keep repeating until the end of the input file
    while(!gzeof($file)) {
        // Read buffer-size bytes
        // Both fwrite and gzread and binary-safe
        fwrite($out_file, gzread($file, $buffer_size));
    }

    // Files are done, close files
    fclose($out_file);
    gzclose($file);
}

$isDownload = false;
if(file_exists("./GeoLite2-$type.mmdb")){
    $localVersion = md5_file("./GeoLite2-$type.mmdb");
    $remoteVersion = file_get_contents("http://geolite.maxmind.com/download/geoip/database/GeoLite2-$type.md5");
    if($localVersion != $remoteVersion){
        $isDownload = true;
        $log->add("Version Mismatch, A Newer Version Will Be Downloaded");
    }
    else{
        $log->add("./GeoLite2-$type.mmdb Is Up-to-date");
        echo "./GeoLite2-$type.mmdb Is Up-to-date";
    }
}else{
    $isDownload = true;
}

if($isDownload){
    //Remove Current Files
    if(file_exists("./GeoLite2-$type.mmdb.gz")) unlink("./GeoLite2-$type.mmdb.gz");
    if(file_exists("./GeoLite2-$type.mmdb")) unlink("./GeoLite2-$type.mmdb");

    $remoteFile = "http://geolite.maxmind.com/download/geoip/database/GeoLite2-$type.mmdb.gz";
    $remoteFile = file_get_contents($remoteFile);
    if($remoteFile){
        $localFile = fopen("./GeoLite2-$type.mmdb.gz", 'wb');
        fwrite($localFile, $remoteFile);
        fclose($localFile);
        ungz("./GeoLite2-$type.mmdb.gz");
        unlink("./GeoLite2-$type.mmdb.gz");
        $log->add("Download Finished, A Newer Version Has Been Extracted");
    }else{
        $log->add('Download Error');
    }
}

$log->add('Cron Script Finished');
exit('Cron Script Finished');