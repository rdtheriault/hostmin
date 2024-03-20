<?php
if(session_status() === PHP_SESSION_NONE){
	session_start();
}
if (!isset($_SESSION['user'] )){
	echo 'Your session expired. Go <a href="../">here</a> to log in';
	die();
}
if ($_SESSION['user'] != "adminUser9876!"){
	echo 'You are not authorized to be here. Go <a href="../">here</a> to log in';
	die();
}
$db = new SQLite3('users.db');

// Function to add a single user
function addUser($name, $username, $password, $isAdmin, $db) {
    // Hash the password before storing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert user
    $stmt = $db->prepare("INSERT INTO users (name, username, password, admin) VALUES (:name, :username, :password, :admin)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':username', strtolower($username), SQLITE3_TEXT);
    $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
    $stmt->bindValue(':admin', $isAdmin, SQLITE3_INTEGER);
    $result = $stmt->execute();

    // Create user folder
    mkdir("../users/$username");
    mkdir("../users/$username/admin");

    // Copy files to user folder
    copy("files/index.php", "../users/$username/admin/index.php");
    copy("files/change.php", "../users/$username/admin/change.php");
	
	fopen("../users/$username/index.php", 'w');

    return $result;
}

// Function to add multiple users from CSV
function addUsersFromCSV($file, $db) {
    $handle = fopen($file, "r");
    if ($handle !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $name = $data[0];
            $username = $data[1];
            $password = $data[2];
            $isAdmin = $data[3];

            // Add user to database
            addUser($name, $username, $password, $isAdmin, $db);
        }
        fclose($handle);
        return true;
    } else {
        return false;
    }
}

// Check if the form to add a single user is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_single_user'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $isAdmin = isset($_POST['admin']) ? 1 : 0;

    // Add user to database
    addUser($name, $username, $password, $isAdmin, $db);
}

// Check if the form to upload CSV is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_csv'])) {
    if ($_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
        $csv_file = $_FILES['csv_file']['tmp_name'];

        // Add users from CSV
        $success = addUsersFromCSV($csv_file, $db);

        if ($success) {
            $csv_message = "CSV file uploaded successfully.";
        } else {
            $csv_error_message = "Failed to upload CSV file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Users</title>
</head>
<body>
    <h2>Add Single User</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name"><br><br>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password"><br><br>
        <input type="checkbox" id="admin" name="admin" value="admin">
        <label for="admin">Admin</label><br><br>
        <input type="submit" name="add_single_user" value="Add User">
    </form>

    <h2>Add Users from CSV</h2>
    <?php if(isset($csv_error_message)) echo "<p>$csv_error_message</p>"; ?>
    <?php if(isset($csv_message)) echo "<p>$csv_message</p>"; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
        <label for="csv_file">Upload CSV file:</label>
        <input type="file" id="csv_file" name="csv_file"><br><br>
        <input type="submit" name="upload_csv" value="Upload CSV">
    </form>
</body>
</html>
