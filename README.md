# Information of IP #

## Description ##

This API can help you get information of a IP.
For example, the country, state, city or even the latitude and longitude of the IP address.

## Usage ##

You can just simply upload these files to your server, then load the page
```
http(s)://yourserver/yourfolder/cron.php
```
This will update/download the IP database onto your server.
If you want only the country information, please edit the cron.php and json.php before you run cron.php:
```
$type = 'City';
```

After the downloading process finishes, you can use this to get information:
```
http(s)://yourserver/yourfolder/json.php?ip=[IP Address]
```

An Example is here: [IX Network API: IP](https://api.ixnet.work/ip/json.php?ip=)
You can just add the IP address behind the link:
```
https://api.ixnet.work/ip/json.php?ip=8.8.8.8
```
And an JSON-formatted data will be returned:
```
{"country":"US","state":"CA","city":"Mountain View","postcode":"94040","latitude":37.3845,"longitude":-122.0881,"database":"GeoIP2Lite Database by Maxmind - http:\/\/www.maxmind.com"}
```

## Further Configuration for Apache Server ##

This is an example of the .htaccess file, which can rewrite the request.
```
<IfModule mod_rewrite.c>
RewriteEngine on

RewriteCond %{HTTP_HOST} ^ip.example.com$
RewriteCond %{REQUEST_URI} !^/ip/
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /ip/json.php?ip=$1
RewriteCond %{HTTP_HOST} ^ip.example.com$
RewriteRule ^(/)?$ ip/json.php?ip= [L]
</IfModule>
```

In this way, when you request for http://ip.example.com, the actual requested file is json.php.
```
http://ip.example.com/8.8.8.8
```
Will actually request for
```
{YOUR_ROOT_FOLDER}/ip/json?ip=8.8.8.8
```

## Versioning ##

The cron.php will check up the latest version from 
```
https://version.ixnet.work/ip_json.program.php
```
and if the versions differ, a notice will appear in your cron.log (or the log file you set)

## Copyright and License ##

Copyright 2016 Howard Liu

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
