<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 *
 *
 */
require('connect.php');

session_start();

date_default_timezone_set('America/Winnipeg');

$email_error = false;
$password_error = false;
$password_verify_error = false;
$missing_field = false;
$email_in_use = false;

if(isset($_POST['submit_registration']))
{
    if(strlen($_POST['register_email']) == 0 || strlen($_POST['register_password']) == 0 || strlen($_POST['verify_password']) == 0)
    {
        $missing_field = true;
    }
    else if(!filter_input(INPUT_POST, 'register_email', FILTER_VALIDATE_EMAIL))
    {
        $email_error = true;
    }
    else
    {
        if($_POST['register_password'] == $_POST['verify_password'])
        {
            $new_user_email = filter_input(INPUT_POST, 'register_email', FILTER_SANITIZE_SPECIAL_CHARS);

            $emails_query = "SELECT user_email FROM users";
            $emails_statement = $db->prepare($emails_query);
            $emails_statement->execute();
            for($i = 0; $i < $emails_statement->rowCount(); $i++)
            {
                $row = $emails_statement->fetch();

                if($row['user_email'] == $new_user_email)
                {
                    $email_in_use = true;
                }
            }

            if(!$email_in_use)
            {
                $new_user_password = filter_input(INPUT_POST, 'register_password', FILTER_SANITIZE_SPECIAL_CHARS);

                $hashed_password = password_hash($new_user_password, PASSWORD_DEFAULT);

                $register_query = "INSERT INTO users (user_email, user_password, user_register) VALUES (:email, :password, :registered)";
                $statement = $db->prepare($register_query);
                $statement->bindValue(':email', $new_user_email);
                $statement->bindValue(':password', $hashed_password);
                $statement->bindValue(':registered', date("Y-m-d H:i:s"));
                $statement->execute();

                header('Location: login.php');
            }

        }
        else
        {
            $password_verify_error = true;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <title>Winnipeg Pokeclub Official Blog</title>
</head>
<header id="banner">
    <a href="index.php"><img src="images/banner.png" alt="logo"></a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="create.php">Make A Post</a></li>
        <li><a href="search.php">Search</a></li>
        <?php if(!isset($_SESSION['current_user'])): ?>
        <li><a href="register.php">Register</a></li>
        <li><a href="login.php">Login</a></li>
        <?php else: ?>
        <?php if ($_SESSION['current_user_admin'] == 1): ?>
        <li><a href="admin.php">Admin Tools</a></li>
        <?php endif ?>
        <li><a href="logout.php">Logout</a></li>
        <?php endif ?>
    </ul>
</header>
<body>
    <?php if(isset($_SESSION['current_user'])): ?>
        <div class="login_redirect">
            <p class="logged_in">You are already signed in as <?= $_SESSION['current_user'] ?>. Click <a href="logout.php">here</a> to sign out.</p>
        </div>
    <?php else: ?>
        <form method="post" action="register.php">
            <label form="register_email">Email:</label>
            <input type="email" name="register_email" value="<?= isset($_POST['register_email']) ? $_POST['register_email'] : "" ?>" />
            <?php if($email_error): ?>
                <p>*Please enter a valid email address.</p>
            <?php endif ?> 
            <?php if($missing_field): ?>
                <p>*Please enter all fields.</p>
            <?php endif ?> 
            <?php if($email_in_use): ?>
                <p>*Email already in use. Please provide a new email.</p>
            <?php endif ?>
            <label for="register_password">Password:</label>
            <input type="password" name="register_password"/>
            <?php if($missing_field): ?>
                <p>*Please enter all fields.</p>
            <?php endif ?> 
            <label for="verify_password">Re-Type Password:</label>
            <input type="password" name="verify_password">
            <?php if($password_verify_error): ?>
                <p>*Passwords did not match. Please try again.</p>
            <?php endif ?>
            <?php if($missing_field): ?>
                <p>*Please enter all fields.</p>
            <?php endif ?> 
            <input type="submit" name="submit_registration">
        </form>
    <?php endif ?>
</body>
</html>