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
 * @Author Howard Liu
 * Author's website: https://www.ixnet.work
 * Version 2.0
 */

if (!isset($_GET['ip'])) {
    exit('Missing Parameter');
}

require "vendor/autoload.php";
require 'Class/Lib/IXLogging.class.php';
require 'Class/IXIPLibraryJson.php';

//Set type, City or Country
$type = 'City';

header('Content-Type: application/json');
$result = IXNetwork\IXIPLibraryJson::getJson($_GET['ip'], $type);
if ($result) {
    exit(json_encode($result));
} else {
    exit(json_encode(['error' => 'Please refer to the log.']));
}