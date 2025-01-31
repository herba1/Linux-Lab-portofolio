<?php
//session start but safely
require_once 'includes/config_session.inc.php';
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

$currentDirectory = "/";

function process_pwd(&$currentDirectory) : string {
    return $currentDirectory . "\n";
}
function process_ls($fileSystem, $currentDirectory): string {
    // Handle root directory special case
    if ($currentDirectory === "/") {
        $currentLevel = $fileSystem["/"];
        return format_directory_contents($currentLevel);
    }
    // Remove trailing slash if present
    $currentDirectory = rtrim($currentDirectory, "/");
    
    // Split path into components
    $path = array_filter(explode("/", $currentDirectory), 'strlen');
    // Start from root
    $currentLevel = $fileSystem["/"];
    // Traverse the path
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
    sort($output); // Sort entries alphabetically
    return implode(" ", $output) . "\n";
}

// Function to change directory
function process_cd(&$currentDirectory, $fileSystem, $dir): string  {
    if ($dir === "..") {
        // Move up to parent directory
        if ($currentDirectory !== "/") {
            //path parts will trim the leading and trailing '/' and seperate the directories into an array
            // ex: /Documents/Subfolder/ -> Documents/Subfolder -> ["Documents", "Subfolder"] 
            $pathParts = explode("/", trim($currentDirectory, "/"));
           //remove the last directory pop_back() from the array 
            array_pop($pathParts); 
            //rebuild the path after the pop back so: ["Documents"] -> /Documents
            $currentDirectory = "/" . implode("/", $pathParts);
            // if the rebuild path is empty than that means we are in the root directory 
            if ($currentDirectory === "") {
                $currentDirectory = "/";
            }
        }
    } else {
        // Handle moving into a subdirectory
        // Split the current directory path into parts
        $path = array_filter(explode("/", trim($currentDirectory, "/")), 'strlen');
        // Start from the root of the file system
        $currentLevel = $fileSystem["/"];
        // Traverse the file system to the current directory
        foreach ($path as $part) {
            if (!isset($currentLevel[$part])) {
                return "Error: Directory not found.\n";
            }
            $currentLevel = $currentLevel[$part];
        }
        // Check if the target directory exists
        if (!isset($currentLevel[$dir]) || !is_array($currentLevel[$dir])) {
            return "Error: Directory not found.\n";
        }
        // Update the current directory path
        $currentDirectory = rtrim($currentDirectory, "/") . "/" . $dir;
    }
    return "";
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
        // Remove trailing slash from current directory if present
        $currentDirectory = rtrim($currentDirectory, "/");
    
        // Split the current directory path into parts
        $pathParts = array_filter(explode("/", $currentDirectory), 'strlen');
        $currentLevel = $filesystem["/"];
    
        // Traverse to the correct directory
        foreach ($pathParts as $part) {
            if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
                return "Error: Invalid directory path.\n";
            }
            $currentLevel = $currentLevel[$part];
        }
        // Check if the file exists in the current directory
        if (!isset($currentLevel[$file])) {
            return "Error: File not found.\n";
        }
        // Check if the specified file is a file (not a directory)
        if (is_array($currentLevel[$file])) {
            return "Error: '$file' is a directory.\n";
        }
        // Otherwise if the file exists and is not a directory then return the file contents
        return ($currentLevel[$file] ?? "[Empty File]") . "\n";
    }
function process_mv(&$filesystem, $currentDirectory, $oldname, $newname)  : void  {
    //mv can rename files or moves files to a different location
    //if the new content does not exist within the current directory and content then its new and will replace the old content name
         $currentDirectory = rtrim($currentDirectory, "/");
         echo $currentDirectory . "\n";
        // Split the current directory path into parts
        $pathParts = array_filter(explode("/", $currentDirectory), 'strlen');
        $currentLevel = &$filesystem["/"];
        // Traverse to the current directory
    foreach ($pathParts as $part) {
        if (!isset($currentLevel[$part]) || !is_array($currentLevel[$part])) {
            echo "Error: Invalid directory path.\n";
            return;
        }
        $currentLevel = &$currentLevel[$part];
    } 
    // Check if the old file/directory exists
    if (!isset($currentLevel[$oldname])) {
        echo "Error: '$oldname' not found.\n";
        return;
    }

    // Check if the new name already exists
    if (isset($currentLevel[$newname])) {
        echo "Error: '$newname' already exists.\n";
        return;
    }
             // Rename the file/directory
    $currentLevel[$newname] = $currentLevel[$oldname]; // Copy the content to the new name
    unset($currentLevel[$oldname]); // Remove the old name

    echo "Renamed '$oldname' to '$newname'.\n";
}

// Main loop
while (true) {
    // Display the prompt with the current directory
    echo "\033[32mexample_user@LinuxLab:\033[0m" . "\033[34m$currentDirectory\033[0m". "\033[32m$\033[0m ";
    // Read input
    $handle = fopen("php://stdin", "r");
    $input = fgets($handle);
    fclose($handle); 
    $input = trim($input); // Remove whitespace and newline

    // Process commands
    if ($input === "ls") {
        echo process_ls($fileSystem, $currentDirectory);
    } elseif (str_starts_with($input, "cd ")) {
        $dir = trim(substr($input, 3)); // Extract the directory name
        $error = process_cd($currentDirectory, $fileSystem, $dir);
        if ($error) {
            echo $error;
        }
    }   elseif (str_starts_with($input, "mkdir ")) {
        // Extract the directory name from the input
        $newdir = trim(substr($input, 6)); // Get everything after "mkdir "
        if ($newdir === "") {
            echo "Error: Directory name not specified.\n";
        } else {
            process_mkdir($fileSystem, $currentDirectory, $newdir);
        }
    }
    else if (str_starts_with($input, "cat")) {
        $file = trim(substr($input, 4));
        echo process_cat($fileSystem, $currentDirectory, $file);
    } 
    else if ($input === "pwd") {
        echo process_pwd($currentDirectory);
    }
    else if ($input === "mv") {
        $s = "";
        $t = "";
        echo process_mv($fileSystem, $currentDirectory, $s, $t);
    }
     elseif ($input === "exit") {
        break;
    } else {
        echo "Command not recognized: $input\n";
    }
}
?>
