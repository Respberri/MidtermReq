<?php
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['uName']) && isset($_POST['pass'])) {
        $username = $_POST['uName'];
        $password = $_POST['pass'];

    // Predefined credentials
    $predefinedUsername = "admin";
    $predefinedPassword = "password";

    // Validate credentials
    if ($username == $predefinedUsername && $password == $predefinedPassword) {
        // Successful login, set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        // Redirect to incorrect credentials page
        header('Location: incorrect.php');
        exit();
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-in Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" id="signIn">
        <h1 class="form-title">Sign In</h1>
        <form action="login.php" method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>    
                <input type="text" name = "uName" placeholder="Username" required>
                <label for="uName">Username</label>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>    
                <input type="password" name = "pass" placeholder="Password" required>
                <label for="pass">Password</label>
            </div>
            <input type="submit" class="btn" value="Sign In" name="signIn"> 
        
        <p class="or">
            ------------- or -------------
        </p>
        <div class="reset">
            <p>Want To Reset Credentials?</p>
            <input type="reset" class="resetBtn" value="Reset" name="reset"> 

        </div>
    </form>
    </div>
</body>
</html>