<?php
//make sure to keep track of location on refresh...
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

//get file name from post
$fullfile = '../'.$_POST['fileName'];
$file = $_POST['fileName'];
//$file = '../index.php'; // Specify the file name

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['content'])) {
    // Get the edited content
    $editedContent = $_POST['content'];

    // Write the edited content back to the file
    file_put_contents($fullfile, $editedContent);

    // Redirect back to the same page after saving
    //header("Location: {$_SERVER['PHP_SELF']}");
    //exit;
}

// Read the content from the file
$content = file_get_contents($fullfile);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit File</title>
</head>
<body>
    <h2>Edit File <?php echo $file; ?></h2>
    <form method="post">
        <textarea name="content" rows="40" cols="150"><?php echo htmlspecialchars($content); ?></textarea><br>
		<input name="fileName" type="hidden" value="<?php echo $file ?>">
        <input type="submit" value="Save">
    </form>
</body>
</html>