<?php
//if (!isset($_SESSION['access']) || $_SESSION['access'] !== 'I Have Access to this Page 1234!?') {
    // If not, redirect to a different page or display an error message
    //echo "You don't have access to this page!";
    //exit;
//}

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
