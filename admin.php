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

$users_query = "SELECT u.user_email, u.user_register, u.user_id, u.user_admin, u.active, count(p.user_id) FROM users u
LEFT JOIN post p ON u.user_id = p.user_id
GROUP BY u.user_id";
$users_statement = $db->prepare($users_query);
$users_statement->execute();


if(isset($_POST['disable_user']))
{
    $user = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $disable_user = "UPDATE users SET active = 0 WHERE user_id = :id";
    $disable_statement = $db->prepare($disable_user);
    $disable_statement->bindValue(':id', $user);
    $disable_statement->execute();

    $disable_posts = "UPDATE post SET active = 0 WHERE user_id = :id";
    $disable_posts_statement = $db->prepare($disable_posts);
    $disable_posts_statement->bindValue(':id', $user);
    $disable_posts_statement->execute();

    header('Location: admin.php');
}

if(isset($_POST['enable_user']))
{
    $user = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $enable_user = "UPDATE users SET active = 1 WHERE user_id = :id";
    $enable_statement = $db->prepare($enable_user);
    $enable_statement->bindValue(':id', $user);
    $enable_statement->execute();

    $enable_posts = "UPDATE post SET active = 1 WHERE user_id = :id";
    $enable_posts_statement = $db->prepare($enable_posts);
    $enable_posts_statement->bindValue(':id', $user);
    $enable_posts_statement->execute();

    header('Location: admin.php');
}

if(isset($_POST['promote_user']))
{
    $user = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $promote_user = "UPDATE users SET user_admin = 1 WHERE user_id = :id";
    $promote_statement = $db->prepare($promote_user);
    $promote_statement->bindValue(':id', $user);
    $promote_statement->execute();

    header('Location: admin.php');
}

if(isset($_POST['demote_user']))
{
    $user = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    $demote_user = "UPDATE users SET user_admin = 0 WHERE user_id = :id";
    $demote_statement = $db->prepare($demote_user);
    $demote_statement->bindValue(':id', $user);
    $demote_statement->execute();

    header('Location: admin.php');
}

$new_user_email_error = false;
$new_user_password_error = false;
$new_user_password_mismatch = false;
$new_user_created = false;

if(isset($_POST['create_user']))
{
    if(filter_input(INPUT_POST, 'new_user_email', FILTER_VALIDATE_EMAIL))
    {
        if(strlen($_POST['new_user_password']) > 0 && strlen($_POST['new_user_password_confirm']) > 0)
        {
            if($_POST['new_user_password'] === $_POST['new_user_password_confirm'])
            {
                if(isset($_POST['make_user_admin']))
                {
                    $user_email = filter_input(INPUT_POST, 'new_user_email', FILTER_SANITIZE_SPECIAL_CHARS);
                    $date = date("Y-m-d H:i:s");
                    $user_password = filter_input(INPUT_POST, 'new_user_password', FILTER_SANITIZE_SPECIAL_CHARS);

                    $new_user_hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

                    $new_user_query = "INSERT INTO users (user_email, user_password, user_register, user_admin) 
                                       VALUES (:email, :password, :created, 1)";

                    $new_user_statement = $db->prepare($new_user_query);
                    $new_user_statement->bindValue(':email', $user_email);
                    $new_user_statement->bindValue(':password', $new_user_hashed_password);
                    $new_user_statement->bindValue(':created', $date);

                    $new_user_statement->execute();

                    header('Location: admin.php');
                }
                else
                {
                    $user_email = filter_input(INPUT_POST, 'new_user_email', FILTER_SANITIZE_SPECIAL_CHARS);
                    $date = date("Y-m-d H:i:s");
                    $user_password = filter_input(INPUT_POST, 'new_user_password', FILTER_SANITIZE_SPECIAL_CHARS);

                    $new_user_hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

                    $new_user_query = "INSERT INTO users (user_email, user_password, user_register, user_admin) 
                                       VALUES (:email, :password, :created, 0)";

                    $new_user_statement = $db->prepare($new_user_query);
                    $new_user_statement->bindValue(':email', $user_email);
                    $new_user_statement->bindValue(':password', $new_user_hashed_password);
                    $new_user_statement->bindValue(':created', $date);

                    $new_user_statement->execute();

                    header('Location: admin.php');
                }   
            }
            else
            {
                $new_user_password_mismatch = true;
            }
        }
        else
        {
            $new_user_password_error = true;
        }
    }
    else
    {
        $new_user_email_error = true;
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
    <?php if ($_SESSION['current_user_admin'] == 1): ?>
    <div id="members">
        <div>
            <form method="post" action="admin.php">
                <h1>Add A User</h1>
                <label>Email:</label>
                <input type="text" name="new_user_email" value="<?= isset($_POST['new_user_email']) ? $_POST['new_user_email'] : "" ?>">
                <?php if($new_user_email_error): ?>
                    <p>*Please enter a valid email.</p>
                <?php endif ?>
                <label>Password:</label>
                <input type="password" name="new_user_password">
                <?php if($new_user_password_error): ?>
                    <p>*Required.</p>
                <?php endif ?>
                <label>Confirm Password:</label>
                <input type="password" name="new_user_password_confirm">
                <?php if($new_user_password_error): ?>
                    <p>*Required.</p>
                <?php endif ?>
                <?php if($new_user_password_mismatch): ?>
                    <p>*Passwords must match.</p>
                <?php endif ?>
                <label>Make User Admin?:</label>
                <input type="checkbox" name="make_user_admin">
                <input type="submit" name="create_user" value="Add User">
            </form>
        </div>
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
                    <button type="submit" name="promote_user">Promote to Admin</button>
                    <button type="submit" name="demote_user">Demote to User</button>
                </form>
            </div>
        <?php endfor ?>
    </div>
    <?php else: ?>
    <h1>You do not have permission to view this page.</h1>
    <?php endif ?>
    <?php else: ?>
    <h1>You do not have permission to view this page.</h1>
    <?php endif ?>
</body>
</html>
