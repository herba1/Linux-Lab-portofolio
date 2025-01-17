<form action="" method="POST">
    Enter a command: <input type="text" name="userInput" autocomplete="off" oninvalid="return true;">
    <button type="submit">Submit</button>
</form>

<?php

function read_command(): string {
    // Ensure the function only runs if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Check if 'userInput' exists in the POST data
        if (!isset($_POST["userInput"])) {
            echo "Error: Input not set. Please try again.";
            exit(1);
        }

        // Retrieve and sanitize the input
        $input = trim($_POST["userInput"]); // Remove leading/trailing spaces

        // Validate that the input is not empty
        if ($input === "") {
            echo "Error: Command is empty. Please enter a valid command.";
            exit(1); // Stop further execution
        }

        // Return the valid input
        return $input;
    }

    // Return an empty string if not a POST request
    return "";
}

// Only process the command if a POST request is made
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $command = read_command();
    echo "<br>Processing command: " . htmlspecialchars($command);
}
?>

