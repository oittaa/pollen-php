<?php
/*

pollen-php: Entropy-as-a-Server web server
Requires PHP 5.4 or newer

  Copyright (C) 2015 Eero Vuojolahti

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, version 3 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

$size = 64;
$algo = "sha512";
$device = "/dev/urandom";
$errortxt = "Please use the pollinate client.  'sudo apt-get install pollinate' or download from: https://bazaar.launchpad.net/~pollinate/pollinate/trunk/view/head:/pollinate\n";

header("Content-Type: text/plain; charset=utf-8");
if (empty($_REQUEST["challenge"])) {
    http_response_code(400);
    exit($errortxt);
}

$challengeresponse = hash($algo, $_REQUEST["challenge"]);
file_put_contents($device, $challengeresponse);

try {
    $bytes = file_get_contents($device, false, null, -1, $size);
    if ($bytes === false) {
        throw new Exception('Failed to read from random device');
    }
    $seed = hash($algo, $bytes);
} catch (Exception $e) {
    http_response_code(500);
    exit($e->getMessage()."\n");
}

printf("%s\n%s\n", $challengeresponse, $seed);
