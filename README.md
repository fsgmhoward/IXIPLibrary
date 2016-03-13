# IX IP Infomation API #
## An IP Information API Dependent on MaxMind IP Database ##

## Description ##

This API can help you get information of a IP.
For example, the country, state, city or even the latitude, longitude or the ISP information of an IP address.

## Installation ##
```
curl -sS https://getcomposer.org/installer | php
php composer.phar require "ix-network/ip-info-api"
```

## How to Use ##
Use this to update your database:
```
include 'vendor/autoload.php';
// when $logfile is null, log will be recorded into Log/Cron.log
IXNetwork\IPLib\Cron::cron([$type = 'City'[, $isLog = true[, $logFile = null]]]);
```

Then you can use this to get information of an IP:
```
include 'vendor/autoload.php';
// when $logfile is null, log will be recorded into Log/Info.log
IXNetwork\IPLib\Info::getInfo($ip[, $type = 'City'[, $isLogging = true[, $logFile = null]]]);
```

## Copyright and License ##

Copyright (C) 2016 Howard Liu

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.