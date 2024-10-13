<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linux Lab Terminal</title>
    <style>
        body {
            font-family: monospace;
            background-color: #1e1e1e;
            color: #d0d0d0;
            padding: 20px;
        }

        #terminal {
            background-color: #000;
            padding: 20px;
            height: 400px;
            width: 800px;
            border: 2px solid #4caf50;
            overflow-y: auto;
            white-space: pre-wrap;
        }

        #inputLine {
            display: flex;
            align-items: center;
        }

        #inputLine #prompt {
            color: #4caf50;
        }

        #commandInput {
            background: none;
            border: none;
            color: #d0d0d0;
            width: 100%;
            outline: none;
        }

        #commandInput:focus {
            outline: none;
        }

        .output {
            white-space: pre-wrap;
        }

        .command-line {
            color: #4caf50;
        }
    </style>
</head>
<body>
    <div id="terminal">
        <div id="output"></div>
        <div id="inputLine">
            <span id="prompt">user@linuxlab:~$ </span>
            <input type="text" id="commandInput" autofocus>
        </div>
    </div>

    <script>
        const terminalOutput = document.getElementById('output');
        const commandInput = document.getElementById('commandInput');
        const promptText = 'user@linuxlab:~$ ';

        let fakeFileSystem = {
            "home": {
                "user": {
                    "documents": ["file1.txt", "file2.txt"],
                    "pictures": ["image1.jpg", "image2.png"]
                }
            },
            "etc": {
                "config": ["config1.conf", "config2.conf"]
            }
        };

        let currentPath = ["home", "user"];

        function getCurrentDirectory() {
            let dir = fakeFileSystem;
            currentPath.forEach(folder => {
                dir = dir[folder];
            });
            return dir;
        }

        function executeCommand(command) {
            let output = "";
            let cmdParts = command.split(" ");
            let mainCommand = cmdParts[0];

            if (mainCommand === "ls") {
                let currentDir = getCurrentDirectory();
                output = Object.keys(currentDir).join("\n");

            } else if (mainCommand === "cd") {
                if (cmdParts[1] === "..") {
                    if (currentPath.length > 1) {
                        currentPath.pop(); // Go up a directory
                    }
                } else if (cmdParts[1] && getCurrentDirectory()[cmdParts[1]]) {
                    currentPath.push(cmdParts[1]); // Go down to specified directory
                } else {
                    output = "bash: cd: " + (cmdParts[1] || '') + ": No such file or directory";
                }

            } else if (mainCommand === "echo") {
                output = cmdParts.slice(1).join(" ");

            } else {
                output = `bash: ${mainCommand}: command not found`;
            }

            return output;
        }

        commandInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                let command = commandInput.value.trim();
                commandInput.value = ''; // Clear the input

                // Display command in terminal
                terminalOutput.innerHTML += `<span class="command-line">${promptText}</span>${command}\n`;

                // Execute command and show the result
                let result = executeCommand(command);
                if (result) {
                    terminalOutput.innerHTML += result + "\n";
                }

                // Scroll to the bottom of the terminal
                terminalOutput.scrollTop = terminalOutput.scrollHeight;
            }
        });
    </script>
</body>
</html>

