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
        return "";
    }  
        
    $targetPath = trim($dir, "/");
    $pathParts = explode("/", $targetPath);

    // Handle the chaining of cd, ex: cd Documents/Subfolder/temp
     if (count($pathParts) > 1) {
        // Start from the root if it's an absolute path, otherwise start from the current directory
        $newDirectory = (substr($dir, 0, 1) === "/") ? "/" : rtrim($currentDirectory, "/");
        // Split current directory into parts
        $path = array_filter(explode("/", trim($newDirectory, "/")), 'strlen');
        // Start from the root of the file system
        $currentLevel = $fileSystem["/"]; //basically initialzing to zero
        // Traverse the file system to the current directory
        foreach ($path as $part) {
            if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
                return "Error: Invalid directory path.\n";
            }
            $currentLevel = &$currentLevel[$part];
        }
        
        //for every directory in the path
        foreach ($pathParts as $part) {
                // Check if the directory exists
                if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
                    return "Error: Directory '$part' not found.\n";
                }
                $currentLevel = &$currentLevel[$part];
                $newDirectory = rtrim($newDirectory, "/") . "/" . $part;
            }
        // Update the current directory
        $currentDirectory = $newDirectory;    
    }
    else {
        $path = array_filter(explode("/", trim($currentDirectory, "/")), 'strlen');
        $currentLevel = $fileSystem["/"];
        foreach ($path as $part) {
            if (!isset($currentLevel[$part])) {
                return "";
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
function process_mkdir(&$fileSystem, $currentDirectory, $newdir): string {
    // Traverse to the current directory in the file system
    $path = array_filter(explode("/", trim($currentDirectory, "/")), 'strlen');
    $currentLevel = &$fileSystem["/"]; // Start from root

    // Traverse the path to reach the current directory
    foreach ($path as $part) {
        $currentLevel = &$currentLevel[$part];
    // Check if the directory already exists
    if (isset($currentLevel[$newdir])) {
        return "Directory already exists: $newdir\n";
        }
    }
    // Create the new directory
    $currentLevel[$newdir] = [];
    return $newdir . "\n";
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

function process_mv(&$filesystem, $currentDirectory, $oldname, $newname): string {
    // Helper function to resolve the target directory and name from the newname
    $resolveNewnamePath = function ($currentDir, $newname) {
        $isAbsolute = substr($newname, 0, 1) === '/';
        $pathParts = array_filter(explode('/', $newname), function($part) {
            return $part !== '';
        });
        $currentParts = $isAbsolute ? [] : array_filter(explode('/', trim($currentDir, '/')), 'strlen');
        
        foreach ($pathParts as $part) {
            if ($part === '..') {
                if (!empty($currentParts)) {
                    array_pop($currentParts);
                }
            } elseif ($part !== '.') {
                $currentParts[] = $part;
            }
        }
        
        if (empty($currentParts)) {
            $targetDir = '/';
            $targetName = '';
        } else {
            $targetName = array_pop($currentParts);
            $targetDir = '/' . implode('/', $currentParts);
        }
        return [$targetDir, $targetName];
    };

    // Navigate to the current directory
    $pathParts = array_filter(explode('/', trim($currentDirectory, '/')), 'strlen');
    $currentLevel = &$filesystem['/'];
    foreach ($pathParts as $part) {
        if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
            return "Error: Invalid current directory path.\n";
        }
        $currentLevel = &$currentLevel[$part];
    }

    // Check if the old item exists
    if (!isset($currentLevel[$oldname])) {
        return "Error: '$oldname' not found.\n";
    }

    // Resolve newname to target directory and target name
    list($targetDir, $targetName) = $resolveNewnamePath($currentDirectory, $newname);
    if ($targetName === '') {
        $targetName = $oldname;
    }

    // Navigate to the initial target directory
    $targetPathParts = array_filter(explode('/', trim($targetDir, '/')), 'strlen');
    $targetLevel = &$filesystem['/'];
    foreach ($targetPathParts as $part) {
        if (!isset($targetLevel[$part]) || !is_array($targetLevel[$part])) {
            return "Error: Target directory '$targetDir' does not exist.\n";
        }
        $targetLevel = &$targetLevel[$part];
    }

    // Check if the target name is an existing directory (determines if we're moving into it)
    $isMoveToDirectory = isset($targetLevel[$targetName]) && is_array($targetLevel[$targetName]);

    // Adjust target directory and name if moving into an existing directory
    if ($isMoveToDirectory) {
        $targetDir = rtrim($targetDir, '/') . '/' . $targetName;
        $targetName = $oldname;
        // Re-navigate to the adjusted target directory
        $adjustedTargetPathParts = array_filter(explode('/', trim($targetDir, '/')), 'strlen');
        $targetLevel = &$filesystem['/'];
        foreach ($adjustedTargetPathParts as $part) {
            if (!isset($targetLevel[$part]) || !is_array($targetLevel[$part])) {
                return "Error: Adjusted target directory '$targetDir' does not exist.\n";
            }
            $targetLevel = &$targetLevel[$part];
        }
    }

    // Check if target name already exists in the target directory
    if (isset($targetLevel[$targetName])) {
        return "Error: '$targetName' already exists in target directory.\n";
    }

    // Perform the move or rename
    $targetLevel[$targetName] = $currentLevel[$oldname];
    unset($currentLevel[$oldname]);

    return "";
}

function process_refresh() : string {
    // Reinitialize session variables to restore the default file system
    global $fileSystem; // Access the original file system structure

    $_SESSION['fileSystem'] = &$fileSystem; // Reset the file system
    $_SESSION['currentDirectory'] = "/";  // Reset to root directory

    return "File system has been reset to default.\n";
}

function process_rm(&$fileSystem, &$currentDirectory, $argument): string {
    // Trim and split the current directory path
    $currentDirectory = rtrim($currentDirectory, "/");
    $path = array_filter(explode("/", $currentDirectory), 'strlen');
    $currentLevel = &$fileSystem["/"]; // Reference to root

    // Traverse to the current directory
    foreach ($path as $part) {
        if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
            return "Error: '$part' not found.\n";
        }
        $currentLevel = &$currentLevel[$part]; // Maintain reference
    }

    // Check if the file exists
    if (!array_key_exists($argument, $currentLevel)) {
        return "Error: File '$argument' not found.\n";
    }

    // Check if the target is a directory
    if (is_array($currentLevel[$argument])) {
        return "Error: '$argument' is a directory. Use 'rmdir' to remove directories.\n";
    }

    // Remove the file
    unset($currentLevel[$argument]);
    return "File '$argument' has been removed.\n"; // Removed incorrect session save
}

function process_rmdir(&$fileSystem, &$currentDirectory, $argument): string {
    // Prevent deleting parent directory with "rmdir .."
    if ($argument === "..") {
        return "Error: Cannot remove parent directory.\n";
    }

    // Trim and split the current directory path
    $currentDirectory = rtrim($currentDirectory, "/");
    $path = array_filter(explode("/", $currentDirectory), 'strlen');
    $currentLevel = &$fileSystem["/"]; // Reference to root

    // Traverse to the current directory
    foreach ($path as $part) {
        if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
            return "Error: Directory '$part' not found.\n";
        }
        $currentLevel = &$currentLevel[$part]; // Maintain reference
    }

    // Check if the target directory exists
    if (!isset($currentLevel[$argument])) {
        return "Error: Directory '$argument' not found.\n";
    }

    // Check if it's actually a directory
    if (!is_array($currentLevel[$argument])) {
        return "Error: '$argument' is not a directory.\n";
    }

    // Check if the directory is empty before deleting
    if (!empty($currentLevel[$argument])) {
        return "Error: Directory '$argument' is not empty. Try 'rm -rf'\n";
    }

    // Otherwise remove the empty directory or file
    unset($currentLevel[$argument]);

    return "'$argument' has been removed.\n";
}
function process_rm_rf(&$fileSystem, &$currentDirectory, $argument): string {
    // Prevent deleting parent directory with "rm -rf .."
    if ($argument === "..") {
        return "Error: Cannot remove parent directory.\n";
    }

    // Trim and split the current directory path
    $currentDirectory = rtrim($currentDirectory, "/");
    $path = array_filter(explode("/", $currentDirectory), 'strlen');
    $currentLevel = &$fileSystem["/"]; // Reference to root

    // Traverse to the current directory (without checking $argument yet)
    foreach ($path as $part) {
        if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
            return "Error: Directory '$part' not found.\n";
        }
        $currentLevel = &$currentLevel[$part]; // Maintain reference
    }

    // Check if the target exists
    if (!isset($currentLevel[$argument])) {
        return "Error: '$argument' not found.\n";
    }

    // Recursively delete if it's a directory
    if (is_array($currentLevel[$argument])) {
        delete_recursive($currentLevel[$argument]); // 🔥 Recursively delete contents
    }

    // Remove the target (file or now-empty directory)
    unset($currentLevel[$argument]);

    return "'$argument' has been removed recursively.\n";
}

 // Helper function to recursively delete a directory's contents.
function delete_recursive(&$directory) {
    foreach ($directory as $key => &$content) {
        if (is_array($content)) {
            delete_recursive($content); // 🔁 Recursive call for subdirectories
        }
        unset($directory[$key]); // Delete files or now-empty directories
    }
}



// Handle the command
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $command = trim($_POST['command'] ?? '');
    $args = preg_split('/\s+/', $command); // Split by whitespac
    $fileSystem = &$_SESSION['fileSystem'];
    $currentDir = &$_SESSION['currentDirectory'];

    $cmd = $args[0] ?? '';
    $arg = $args[1] ?? '';
    $arg2 = $args[2] ?? '';
    $output = "";
    switch ($cmd) {
        case 'ls':
            $output = process_ls($fileSystem, $currentDir);
            break;
        case 'cd':
            $output = process_cd($currentDir, $fileSystem, $arg);
            break;
        case 'cat':
            $output = process_cat($fileSystem, $currentDir, $arg);
            break;
        case 'pwd':
            $output = process_pwd($currentDir);
            break;
        case 'mkdir':
            $output = process_mkdir($fileSystem, $currentDir, $arg);
            break;
        case 'mv':
            $output = process_mv($fileSystem, $currentDir, $arg, $arg2);
            break;
        case 'rm':
            if ($arg == "-rf") {
                $output = process_rm_rf($fileSystem, $currentDir, $arg2);
            }
            else {
            $output = process_rm($fileSystem, $currentDir, $arg);
            }
            break;
        case 'rmdir':
            $output = process_rmdir( $fileSystem, $currentDir, $arg);
            break;
        case 'refresh':
            $output = process_refresh();
            break;
        default:
            $output = "Command not recognized: $cmd\n";
            break;
    }

    // Return the output as JSON
    echo json_encode([
        'output' => $output,
        'currentDirectory' => $currentDir
    ]);
} 