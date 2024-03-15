<?php
// Function to list files and folders in a directory
function listFilesAndFolders($dir) {
    // Check if the directory exists and is readable
    if (is_dir($dir) && is_readable($dir)) {
        // Open the directory
        if ($dh = opendir($dir)) {
            // Loop through all the items in the directory
            while (($item = readdir($dh)) !== false) {
                // Skip . and .. directories
                if ($item != '.' && $item != '..') {
                    // If it's a directory, make it clickable
                    if (is_dir("$dir/$item")) {
                        echo "<a href=\"?dir=" . urlencode("$dir/$item") . "\">$item/</a><br>";
                    } else {
                        // If it's a file, make the name clickable and post its name to another page
                        echo "<div class='file' onclick=\"postFileName('$item')\">$item</div><br>";
                    }
                }
            }
            // Close the directory handle
            closedir($dh);
        }
    } else {
        echo "Directory not accessible.";
    }
}

// Get the directory to list (default to the current directory)
if (isset($_GET['dir'])){
	$directory =  $_GET['dir'];
}
else{
	$directory = "../";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Explorer</title>
	<style>
		.file {
			color: #069;
			text-decoration: underline;
			cursor: pointer;
		}
	</style>
    <script>
        // JavaScript function to post file name to another page
        function postFileName(fileName) {
            var form = document.createElement('form');
            form.method = 'post';
            form.action = 'change.php'; 
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'fileName';
            input.value = fileName;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <h2>File Explorer</h2>
    <?php
    // List files and folders in the specified directory
    listFilesAndFolders($directory);
    ?>
</body>
</html>