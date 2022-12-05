<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 *
 *
 */
require('connect.php');

session_start();

$errors = false;

$requested_post = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$query = "SELECT p.blog_id, p.user_id, p.post_title, p.post_desc, p.post_date, p.image_file, p.active, u.user_email
          FROM post p JOIN users u ON p.user_id = u.user_id WHERE blog_id = :id";
$statement = $db->prepare($query);
$statement->bindValue(':id', $requested_post);
$statement->execute();
$row = $statement->fetch();



if(isset($_POST['update']))
{
    if(isset($_POST['image_remove']))
    {
        $image_name = $_POST['image_remove'];
        $author = $_POST['author'];
        unlink("images/{$author}/{$image_name}");

        $blog_id = filter_input(INPUT_POST, 'blog_id', FILTER_SANITIZE_NUMBER_INT);

        $remove_image_query = "UPDATE post SET image_file = NULL WHERE blog_id = :id";
        $remove_statement = $db->prepare($remove_image_query);
        $remove_statement->bindValue(':id', $blog_id);
        $remove_statement->execute();
    }

    if(strlen($_POST['title']) > 1 && strlen($_POST['content']) > 1)
    {
        $new_title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $new_content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
        $blog_id = filter_input(INPUT_POST, 'blog_id', FILTER_SANITIZE_NUMBER_INT);

        $update_query = "UPDATE post SET post_title = :title, post_desc = :description WHERE blog_id = :id";
        $update_statement = $db->prepare($update_query);
        $update_statement->bindValue(':title', $new_title);
        $update_statement->bindValue(':description', $new_content);
        $update_statement->bindValue(':id', $blog_id);
        $update_statement->execute();

        header('Location: index.php');
    }
    else
    {
        $errors = true;
        $blog_id = filter_input(INPUT_POST, 'blog_id', FILTER_SANITIZE_NUMBER_INT);
        $location = 'Location: edit.php?id=' . $blog_id;
        header($location);
    }
}
else if(isset($_POST['delete']))
{
    $delete_query = "DELETE FROM post WHERE blog_id = :id LIMIT 1";

    $blog_id = filter_input(INPUT_POST, 'blog_id', FILTER_SANITIZE_NUMBER_INT);

    $delete_statement = $db->prepare($delete_query);
    $delete_statement->bindValue(':id', $blog_id);
    $delete_statement->execute();

    header('Location: index.php');
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
	<title>Edit Post</title>
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
    <form method="get" action="search.php">
        <label>Search our Blogs:</label>
        <input type="text" name="searchquery">
        <input type="submit" value="Search">
    </form>
    <?php if(isset($_SESSION['current_user'])): ?>
    <form method="post" action="edit.php">
        <?php if($statement->rowCount() > 0): ?>
            <?php if($_SESSION['current_user_id'] == $row['user_id'] || $_SESSION['current_user_admin'] == 1): ?>
            <label for="title">Post Title:</label>
            <input type="text" name="title" value="<?= $row['post_title'] ?>">
            <?php if($errors): ?>
                <p>*Must contain 1 or more characters.</p>
            <?php endif ?>
        
            <label for="content">Blog Content:</label>
            <textarea name="content" ><?= $row['post_desc'] ?></textarea>
            <?php if($errors): ?>
                <p>*Must contain 1 or more characters.</p>
            <?php endif ?>
            <?php if($row['image_file'] != NULL && $row['image_file'] != ""): ?>
            <p>Remove image <?= $row['image_file'] ?> ?</p>
            <input type="checkbox" name="image_remove" value="<?= $row['image_file'] ?>">
            <?php endif ?>
            <input type="hidden" name="author" value="<?= $row['user_email'] ?>" />
            <input type="hidden" name="blog_id" value="<?= $row['blog_id'] ?>" />
            <input type="submit" name="update" value="Update Post" />
            <input type="submit" name="delete" value="Delete Post" />
            <?php else: ?>
            <p id="permission_error">You do not have permission to edit this post.</p>
            <?php endif ?>
        <?php endif ?>
    </form>
    <?php else: ?>
        <h1>Please <a href="login.php">sign in</a> to view this page.</h1>
    <?php endif ?>

</body>
</html>
