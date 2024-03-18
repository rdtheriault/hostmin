<?php
// Assuming your SQLite database is named 'users.db' and located in the same directory as this PHP script
$db = new SQLite3('admin/users.db');

// Function to authenticate user credentials
function authenticateUser($username, $password, $db) {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();

    $user = $result->fetchArray(SQLITE3_ASSOC);
	
    if ($user && $password == $user['password']) {
		echo "Words";
        return $user;
    } else {
        return false;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Authenticate the user
    $user = authenticateUser($username, $password, $db);
	echo "test";
    if ($user) {
        // Redirect users based on their role
        if ($user['admin'] == 1) {
            header("Location: admin/");
            exit();
        } else {
            header("Location: users/{$user['username']}/");
            exit();
        }
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if(isset($error_message)) echo "<p>$error_message</p>"; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
