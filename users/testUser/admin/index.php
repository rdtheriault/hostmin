<?php

// Get the directory to list (default to the current directory)
if (isset($_POST['fileName'])){
	$directory =  $_POST['fileName'];
}
else{
	$directory = "../";
}
$folder = str_replace("../", "", $directory);
if ($folder != ""){
	$folder .= "/";
}
// Handle form submission to create a file or folder
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name']) && isset($_POST['type'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
	//$folder = $_POST['path'];

    // Check if the name is not empty
    if (!empty($name)) {
        // Sanitize the name to prevent directory traversal
        $name = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $name);

        // Determine the path to create the file or folder
        $path = $directory . '/' . $name;
		echo $path;
        // Check if the file or folder already exists
        if ($type === 'file' && !file_exists($path)) {
            // Create the file
            fopen($path, 'w');
        } elseif ($type === 'folder' && !file_exists($path)) {
            // Create the folder
            mkdir($path);
        } else {
            echo "File or folder already exists!";
        }
    } else {
        echo "Name cannot be empty!";
    }
}

// Function to list files and folders in a directory
function listFilesAndFolders($dir, $folder) {
    // Check if the directory exists and is readable
    if (is_dir($dir) && is_readable($dir)) {
        // Open the directory
        if ($dh = opendir($dir)) {
            // Loop through all the items in the directory
            while (($item = readdir($dh)) !== false) {
                // Skip . and .. directories
                if ($item != '.' && $item != '..' && $item != 'admin') {
                    // If it's a directory, make it clickable
                    if (is_dir("$dir/$item")) {
                        echo "<div class='file' onclick=\"postFolderName('../$item')\">$item</div><br>";
                    } else {
                        // If it's a file, make the name clickable and post its name to another page
                        echo "<div class='file' onclick=\"postFileName('$folder$item')\">$item</div><br>";
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
		function postFolderName(folderName) {
            var form = document.createElement('form');
            form.method = 'post';
            form.action = 'index.php'; 
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'fileName';
            input.value = folderName;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <h2>File Explorer - <?php echo $directory; ?></h2>
    <?php
    // List files and folders in the specified directory
    listFilesAndFolders($directory, $folder);
    ?>
	
	
	
	
	<hr>
    <h3>Create File or Folder</h3>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <select name="type" required>
            <option value="file">File</option>
            <option value="folder">Folder</option>
        </select>
		<input type="hidden" value="<?php echo $directory; ?>" name="fileName">
        <input type="submit" value="Create">
    </form>
	
</body>
</html>
