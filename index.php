<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 *
 *
 */
require('connect.php');

session_start();

$query = "SELECT * FROM post ORDER BY blog_id DESC";

$statement = $db->prepare($query);

$statement->execute();

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
    <p id="user">Welcome, <?= $_SESSION['current_user'] ?></p>
    <?php endif ?>
    <form method="get" action="search.php">
        <label>Search our Blogs:</label>
        <input type="text" name="searchquery">
        <input type="submit" value="Search">
    </form>
    <article>
        <?php if($statement->rowCount() > 0): ?>
            <?php for($i = 0; $i < 5; $i++): ?>
                <?php $row = $statement->fetch() ?>
                <?php if($row): ?>
                <a href="read.php?id=<?= $row['blog_id'] ?>"><div id="mainpage_post">
                    <h1>Title: <?= $row['post_title'] ?></h1>
                    <p>Date Posted: <?= $row['post_date'] ?></p>
                    <p>Content: <?= $row['post_desc'] ?></p>
                    <?php if(isset($_SESSION['current_user'])): ?>
                        <?php if($_SESSION['current_user_id'] == $row['user_id'] || $_SESSION['current_user_admin'] == 1): ?>
                        <p><a class="editlink" href="edit.php?id=<?= $row['blog_id'] ?>">Edit Post</a></p>
                        <?php endif ?>
                    <?php endif ?>
                </div></a>
                <?php endif ?>
            <?php endfor ?>
        <?php else: ?>
        <h1>There are currently no blog posts to display!</h1>
        <?php endif ?>
    </article>


</body>
</html>