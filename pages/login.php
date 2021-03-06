<?php
require_once 'config.php';

$username = $password = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){
        $username_err = 'Please enter username.';
    } else{
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }

    if(empty($username_err) && empty($password_err)){

        $sql = "SELECT username, password FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql)){

            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = $username;

            if(mysqli_stmt_execute($stmt)){

                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){

                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){

                            session_start();
                            $_SESSION['username'] = $username;
                            header("location: ../index.html");
                        } else{

                            $password_err = 'The password you entered was not valid.';
                        }
                    }
                } else{

                    $username_err = 'No account found with that username.';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../CSS/cssreset.css">
    <link rel="stylesheet" type="text/css" href="../CSS/stylesheet.css">
</head>
<body>
    <a href="../homepage.php"><img id="logo" src="../IMG/logo/agr-logo-white.svg"></a>
    <div id="background_login_page">
        <div class="parent_login_form">
            <h1 class="login_title">LOGIN</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" placeholder="Username">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p class="no_account_link">Don't have an account? <a class="a_no_account" href="register.php">Sign up now</a>.</p>
                <p>Go back to <a class="a_no_account" href="../index.html">the homepage</a>.</p>
            </form>
        </div>
    </div>    
</body>
</html>