<?php
if(session_status() === PHP_SESSION_NONE) session_start();

$folders = explode("/", $_SERVER["PHP_SELF"]);

if (!isset($_SESSION['user'] )){
	echo 'Your session expired. Go <a href="../">here</a> to log in';
	die();
}
if (strtolower($_SESSION['user']) != strtolower($folders[2])){
	echo 'You are not authorized to be here. Go <a href="../">here</a> to log in';
	die();
}

// Get the directory to list (default to the current directory)
if (isset($_POST['fileName'])){
	if ($_POST['fileName'] == ".."){
		//get rid of last folder
		$parts = explode('/',$_SESSION['path']);
		$new = "";
		for ($i = 1; $i < count($parts)-1; $i++){
			$new .= '/'.$parts[$i];
		}
		$directory = "..".$new;
		//check if root and fix
		if ($directory == '..'){
			$directory = "../";
		}
	}else if ($_POST['fileName'] == "/////"){//use same path for creating a file/folder
		$directory =  $_SESSION['path'];
	}else{
		if ($_SESSION['path'] == "../"){
			$directory =  $_SESSION['path'].$_POST['fileName'];
		}else{
			$directory =  $_SESSION['path'].'/'.$_POST['fileName'];
		}
	}
	
}
else{
	$directory = "../";
}
$_SESSION['path'] = $directory;

$folder = str_replace("../", "", $directory);
if ($folder != ""){
	$folder .= "/";
}
// Handle form submission to create a file or folder
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name']) && isset($_POST['type']) && !isset($_POST['pic'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
	//$folder = $_POST['path'];

    // Check if the name is not empty
    if (!empty($name)) {
        // Sanitize the name to prevent directory traversal
        $name = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $name);

        // Determine the path to create the file or folder
        $path = $directory . '/' . $name;

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
// Handle form submission to upload pic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pic'])) {

    // Check if the name is not empty
    if ( $_POST['pic'] == "yes") {
		$uploadedFile = $_FILES["file"];
		
        // Determine the path to create the file or folder
        $path = $directory.basename($uploadedFile["name"]);
		
        // Check if the file or folder already exists
        if (!file_exists($path)) {
            // Check if uploaded file is an image
            if ($_FILES['file']['error'] === UPLOAD_ERR_OK && getimagesize($_FILES['file']['tmp_name']) !== false) {
                // Move the uploaded file to the destination folder
                move_uploaded_file($_FILES['file']['tmp_name'], $path);
            } else {
                echo "File upload failed. Please upload only images.";
            }
		}else {
            echo "File already exists!";
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
                if ($item != '.' && $item != 'admin') {
					if ($dir != '../'){
						// If it's a directory, make it clickable
						if (is_dir("$dir/$item") && $item == '..') {
							echo "<div class='file' onclick=\"postFolderName('..')\">$item</div><br>";
						}
						else if (is_dir("$dir/$item")) {
							echo "<div class='file' onclick=\"postFolderName('$item')\">$item (Folder)</div><br>";
						} else {
							// Check if the file is a picture
							$extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
							$pictureExtensions = array('jpg', 'jpeg', 'png', 'gif');
							if (!in_array($extension, $pictureExtensions)) {
								// If it's not a picture, make it clickable
								echo "<div class='file' onclick=\"postFileName('$folder$item')\">$item</div><br>";
							} else {
								// If it's a picture, just display the name
								echo "<div>$item (Picture)</div><br>";
							}
						}
					}
					else if ($item != '..' ){
						// If it's a directory, make it clickable
						if (is_dir("$dir/$item")) {
							echo "<div class='file' onclick=\"postFolderName('$item')\">$item (Folder)</div><br>";
						} else {
							// Check if the file is a picture
							$extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
							$pictureExtensions = array('jpg', 'jpeg', 'png', 'gif');
							if (!in_array($extension, $pictureExtensions)) {
								// If it's not a picture, make it clickable
								echo "<div class='file' onclick=\"postFileName('$folder$item')\">$item</div><br>";
							} else {
								// If it's a picture, just display the name
								echo "<div>$item (Picture)</div><br>";
							}
						}
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
			form.target = "_blank";
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
		<input type="hidden" value="/////" name="fileName">
        <input type="submit" value="Create">
    </form>
	<br><br>
	<hr>
    <h3>Upload Pic</h3>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
		<input type="file" name="file" accept="image/*">
		<input type="hidden" value="/////" name="fileName">
		<input type="hidden" value="yes" name="pic">
        <input type="submit" value="Upload">
    </form>
</body>
</html>
