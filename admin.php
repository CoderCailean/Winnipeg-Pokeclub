<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 *
 *
 */
require('connect.php');

session_start();

$users_query = "SELECT u.user_email, u.user_register, u.user_id, count(p.user_id) FROM users u
JOIN post p ON u.user_id = p.user_id
GROUP BY p.user_id";
$users_statement = $db->prepare($users_query);
$users_statement->execute();

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
	<title>Admin Tools</title>
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
    <p id="user">Welcome, <?= $_SESSION['current_user'] ?></p>
    <?php endif ?>
    <?php if ($_SESSION['current_user_admin'] == 1): ?>
    <div id="members">
        <?php for($i = 0; $i < $users_statement->rowCount(); $i++): ?>
            <?php $row = $users_statement->fetch() ?>
            <div class="member">
                <ul id="<?= $row['user_id'] ?>">
                    <li>Username: <?= $row['user_email'] ?></li>
                    <li>Member Since: <?= $row['user_register'] ?></li>
                    <li>Number of Posts: <?= $row['count(p.user_id)'] ?></li>
                </ul>
            </div>
        <?php endfor ?>
    </div>
    <?php else: ?>
    <h1>You do not have permission to view this page.</h1>
    <?php endif ?>
</body>
</html>