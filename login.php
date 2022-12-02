<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 * This page allows users to login to the website. Credentials are validated and the user is redirected upon login.
 *
 */
require('connect.php');

session_start();

$user_logged_in = false;

if(isset($_SESSION['current_user']))
{
	$user_logged_in = true;
}

$login_error = false;
$email_error = false;
$password_error = false;

if(isset($_POST['login']))
{
	if(strlen($_POST['email']) == 0 || strlen($_POST['password']) == 0)
	{
		$login_error = true;
	}
	else
	{
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
		$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

		$login_query = "SELECT * FROM users WHERE user_email = :email";
		$statement = $db->prepare($login_query);
		$statement->bindValue(':email', $email);
		$statement->execute();
		$row = $statement->fetch();

		if($row)
		{
			if(password_verify($password, $row['user_password']))
			{
				$_SESSION['current_user'] = $row['user_email'];
				$_SESSION['current_user_id'] = $row['user_id'];
				$_SESSION['current_user_admin'] = $row['user_admin'];


				header('Location: index.php');
			}
			else
			{
				$password_error = true;
			}
		}
		else
		{
			$email_error = true;
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
	<?php if(!$user_logged_in): ?>
	<form method="post" action="login.php">
		<label for="email">Email:</label>
		<input type="text" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : "" ?>">
		<?php if($login_error): ?>
			<p>*Required field.</p>
		<?php endif ?>
		<?php if($email_error): ?>
			<p>*Email provided is incorrect. Please try again.</p>
		<?php endif ?>
		<label for="password">Password</label>
		<input type="password" name="password">
		<?php if($login_error): ?>
			<p>*Required field.</p>
		<?php endif ?>
		<?php if($password_error): ?>
			<p>*Password provided is incorrect. Please try again.</p>
		<?php endif ?>
		<input type="submit" value="Login" name="login">
	</form>
	<?php else: ?>
	<h1>You are already signed in as <?= $_SESSION['current_user'] ?>. Click <a href="logout.php">here</a> to logout.</h1>
	<?php endif ?>
</body>
</html>