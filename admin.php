<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 *
 *
 */
require('connect.php');

session_start();

$updating = false;

$users_query = "SELECT u.user_email, u.user_register, u.user_id, u.user_admin, u.active, count(p.user_id) FROM users u
LEFT JOIN post p ON u.user_id = p.user_id
GROUP BY p.user_id";
$users_statement = $db->prepare($users_query);
$users_statement->execute();


if(isset($_POST['disable_user']))
{
    $user = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $disable = "UPDATE users SET active = 0 WHERE user_id = :id";
    $disable_statement = $db->prepare($disable);
    $disable_statement->bindValue(':id', $user);
    $disable_statement->execute();

    header('Location: admin.php');
}

if(isset($_POST['enable_user']))
{
    $user = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $user = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $enable = "UPDATE users SET active = 1 WHERE user_id = :id";
    $enable_statement = $db->prepare($enable);
    $enable_statement->bindValue(':id', $user);
    $enable_statement->execute();

    header('Location: admin.php');
}

if(isset($_POST['update_user']))
{
    $updating = true;
}

if(isset($_POST['submit_update']))
{
    if(filter_input(INPUT_POST, 'user_email_update', FILTER_VALIDATE_EMAIL))
    {
        $new_email = filter_input(INPUT_POST, 'user_email_update', FILTER_SANITIZE_SPECIAL_CHARS);
        $user_access = filter_input(INPUT_POST, 'access_level', FILTER_SANITIZE_SPECIAL_CHARS);
        $user_id = filter_input(INPUT_POST, 'user_being_updated', FILTER_SANITIZE_NUMBER_INT);

        $update_query = "UPDATE users SET user_email = :email, user_admin = :access WHERE user_id = :id";
        $update_statement = $db->prepare($update_query);
        $update_statement->bindValue(':email', $new_email);
        $update_statement->bindValue(':access', $user_access);
        $update_statement->bindValue(':id', $user_id);
        $update_statement->execute();

        header('Location: admin.php');
    }
    
}


if(isset($_SESSION['current_user']))
{
    $index_of_at = strpos($_SESSION['current_user'], '@');
    $username = substr($_SESSION['current_user'], 0, $index_of_at);
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
        <li><a href="admin.php">Admin Center</a></li>
        <?php endif ?>
        <li><a href="logout.php">Logout</a></li>
        <?php endif ?>
    </ul>
</header>
<body>
    <?php if(isset($_SESSION['current_user'])): ?>
    <p id="user">Welcome, <?= $username ?></p>
    <?php endif ?>
    <?php if ($_SESSION['current_user_admin'] == 1): ?>
    <div id="members">
        <?php if($updating): ?>
            <form method="post" action="admin.php">
                <label>User Email:</label>
                <input type="email" name="user_email_update" value="<?= $_POST['user_email'] ?>">
                <label>Access Level (1 for Admin, 0 for User):</label>
                <input type="text" name="access_level" value="<?= $_POST['user_admin'] ?>">
                <input type="hidden" name="user_being_updated" value="<?= $_POST['user_id'] ?>" />
                <button type="submit" name="submit_update">Update User</button>
            </form>
        <?php endif ?>
        <?php for($i = 0; $i < $users_statement->rowCount(); $i++): ?>
            <?php $row = $users_statement->fetch() ?>
            <div class="member">
                <ul id="<?= $row['user_id'] ?>">
                    <li>Username: <?= $row['user_email'] ?></li>
                    <li>Member Since: <?= $row['user_register'] ?></li>
                    <li>Admin: <?= $row['user_admin'] == 1 ? "Yes" : "No" ?></li>
                    <li>Number of Posts: <?= $row['count(p.user_id)'] ?></li>
                    <li>Active: <?= $row['active'] == 1 ? "Yes" : "No" ?></li>
                </ul>
                <form method="post" action="admin.php">
                    <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>" />
                    <input type="hidden" name="user_email" value="<?= $row['user_email'] ?>" />
                    <input type="hidden" name="user_admin" value="<?= $row['user_admin'] ?>" />
                    <button type="submit" name="disable_user">Disable User</button>
                    <button type="submit" name="enable_user">Enable User</button>
                    <button type="submit" name="update_user">Update User</button>
                </form>
            </div>
        <?php endfor ?>
    </div>
    <?php else: ?>
    <h1>You do not have permission to view this page.</h1>
    <?php endif ?>
</body>
</html>
