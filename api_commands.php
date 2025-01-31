<?php
require 'includes/config_session.inc.php';
// Debugging: Log the request method and POST data
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . print_r($_POST, true));
// Add these headers at the top of the PHP script
header('Access-Control-Allow-Origin: *'); // Allow all origins (for development)
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Simulated file system
$fileSystem = [
    "/" => [
        "Documents" => [
            "hello.txt" => "
               __ 
              / _) .. ROAR!!!
     _.----._/ /
    /          /
 __/  (  |  (  |
/__.-'|_|--|__|
",
            "Subfolder" => [
                "file1.txt" => null,
                "AnotherFolder" => [
                    "nested_file.txt" => null
                ]
            ]
        ],
        "Pictures" => [
            "photo.jpg" => null
        ],
        "Videos" => [],
        "Projects" => [
            "project1" => [
                "file2.txt" => null
            ]
        ]
    ]
];

if (!isset($_SESSION['fileSystem'])) {
    $_SESSION['fileSystem'] = $fileSystem;
}

if (!isset($_SESSION['currentDirectory'])) {
    $_SESSION['currentDirectory'] = "/";
}

function process_ls($fileSystem, $currentDirectory): string {
    if ($currentDirectory === "/") {
        $currentLevel = $fileSystem["/"];
        return format_directory_contents($currentLevel);
    }
    $currentDirectory = rtrim($currentDirectory, "/");
    $path = array_filter(explode("/", $currentDirectory), 'strlen');
    $currentLevel = $fileSystem["/"];
    foreach ($path as $part) {
        if (!isset($currentLevel[$part])) {
             return "Directory not found.\n";
        }
        if (!is_array($currentLevel[$part])) {
            return "Not a directory.\n";
        }
        $currentLevel = $currentLevel[$part]; 
    }
    return format_directory_contents($currentLevel);
}

function format_directory_contents(array $contents): string {
    if (empty($contents)) {
        return "This directory is empty.\n";
    }
    $output = [];
    foreach ($contents as $name => $content) {
        if (is_array($content)) {
            $output[] = $name . "/";
        } else {
            $output[] = $name;
        }
    }
    sort($output);
    return implode(" ", $output) . "\n";
}

function process_cd(&$currentDirectory, $fileSystem, $dir): string  {
    if ($dir === "..") {
        if ($currentDirectory !== "/") {
            $pathParts = explode("/", trim($currentDirectory, "/"));
            array_pop($pathParts); 
            $currentDirectory = "/" . implode("/", $pathParts);
            if ($currentDirectory === "") {
                $currentDirectory = "/";
            }
        }
    } else {
        $path = array_filter(explode("/", trim($currentDirectory, "/")), 'strlen');
        $currentLevel = $fileSystem["/"];
        foreach ($path as $part) {
            if (!isset($currentLevel[$part])) {
                return "Error: Directory not found.\n";
            }
            $currentLevel = $currentLevel[$part];
        }
        if (!isset($currentLevel[$dir]) || !is_array($currentLevel[$dir])) {
            return "Error: Directory not found.\n";
        }
        $currentDirectory = rtrim($currentDirectory, "/") . "/" . $dir;
    }
    return "";
}
function process_pwd($currentDirectory) : string {
    return $currentDirectory . "\n";
}
function process_mkdir(&$fileSystem, $currentDirectory, $newdir): void {
    // Traverse to the current directory in the file system
    $path = array_filter(explode("/", trim($currentDirectory, "/")), 'strlen');
    $currentLevel = &$fileSystem["/"]; // Start from root

    // Traverse the path to reach the current directory
    foreach ($path as $part) {
        if (!isset($currentLevel[$part])) {
            echo "Error: Directory not found.\n";
            return;
        }
        $currentLevel = &$currentLevel[$part];
    }

    // Check if the directory already exists
    if (isset($currentLevel[$newdir])) {
        echo "Directory already exists: $newdir\n";
        return;
    }

    // Create the new directory
    $currentLevel[$newdir] = [];
    echo "Directory '$newdir' created\n";
}
function process_cat(&$filesystem, $currentDirectory, $file): string {
    $currentDirectory = rtrim($currentDirectory, "/");
    $pathParts = array_filter(explode("/", $currentDirectory), 'strlen');
    $currentLevel = $filesystem["/"];
    foreach ($pathParts as $part) {
        if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
            return "Error: Invalid directory path.\n";
        }
        $currentLevel = $currentLevel[$part];
    }
    if (!isset($currentLevel[$file])) {
        return "Error: File not found.\n";
    }
    if (is_array($currentLevel[$file])) {
        return "Error: '$file' is a directory.\n";
    }
    return ($currentLevel[$file] ?? "[Empty File]") . "\n";
}

// Handle the command
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $command = $_POST['command'] ?? '';
    $args = explode(" ", $command, 2);
    $cmd = $args[0] ?? '';
    $arg = $args[1] ?? '';

    $output = "";
    switch ($cmd) {
        case 'ls':
            $output = process_ls($_SESSION['fileSystem'], $_SESSION['currentDirectory']);
            break;
        case 'cd':
            $output = process_cd($_SESSION['currentDirectory'], $_SESSION['fileSystem'], $arg);
            break;
        case 'cat':
            $output = process_cat($_SESSION['fileSystem'], $_SESSION['currentDirectory'], $arg);
            break;
        case 'pwd';
            $output = process_pwd($_SESSION['currentDirectory']);
            break;
        default:
            $output = "Command not recognized: $cmd\n";
            break;
    }

    // Return the output as JSON
    echo json_encode([
        'output' => $output,
        'currentDirectory' => $_SESSION['currentDirectory']
    ]);
}