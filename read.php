<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 *
 *
 */
require('connect.php');

session_start();

$requested_post = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);


$pokemon_query = "SELECT p.blog_id, p.post_title, p.post_desc, p.post_date, p.user_id, p.image_file, po.pokemon_name,
                  po.pokemon_desc, c.ct_name, co.ct_name, u.user_email FROM post p
                  JOIN pokemon po ON p.pokemon_id = po.pokemon_id
                  JOIN category_type c ON po.ct_id_pokemon_type = c.ct_id
                  JOIN category_type co ON po.ct_id_pokemon_category = co.ct_id
                  JOIN users u ON p.user_id = u.user_id
                  WHERE p.blog_id = :id";
$pokemon_statement = $db->prepare($pokemon_query);
$pokemon_statement->bindValue(':id', $requested_post);
$pokemon_statement->execute();

$gym_query = "SELECT p.blog_id, p.post_title, p.post_desc, p.post_date, p.user_id, p.image_file, g.gym_leader,
              g.gym_description, c.ct_name, u.user_email FROM post p
              JOIN gym g ON p.gym_id = g.gym_id
              JOIN category_type c ON g.ct_id_type = c.ct_id
              JOIN users u ON p.user_id = u.user_id
              WHERE p.blog_id = :id";
$gym_statement = $db->prepare($gym_query);
$gym_statement->bindValue(':id', $requested_post);
$gym_statement->execute();

$location_query = "SELECT p.blog_id, p.post_title, p.post_desc, p.post_date, p.user_id, p.image_file, l.location_name,
                   l.location_description, u.user_email FROM post p
                   JOIN location l ON p.location_id = l.location_id
                   JOIN users u ON p.user_id = u.user_id
                   WHERE p.blog_id = :id";
$location_statement = $db->prepare($location_query);
$location_statement->bindValue(':id', $requested_post);
$location_statement->execute();


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
	<title>Read a Post</title>
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
    <article id="read_post">
        <div>
            <?php if($pokemon_statement->rowCount() > 0): ?>
                <?php $row = $pokemon_statement->fetch() ?>
                <h1>Title: <?= $row['post_title'] ?></h1>
                <p>Date Created: <?= $row['post_date'] ?></p>
                <?php if($row['image_file'] != NULL): ?>
                    <img src="images/<?= $row['user_email'] ?>/<?= $row['image_file'] ?>">
                <?php endif ?>
                <p>Discussion Topic: <?= $row['pokemon_name'] ?> - <?= $row['pokemon_desc'] ?></p>
                <p>Blog Content: <?= $row['post_desc'] ?></p>
                <?php if(isset($_SESSION['current_user'])): ?>
                    <?php if($_SESSION['current_user_id'] == $row['user_id'] || $_SESSION['current_user_admin'] == 1): ?>
                    <p><a class="editlink" href="edit.php?id=<?= $row['blog_id'] ?>">Edit Post</a></p>
                    <?php endif ?>
                <?php endif ?>
            <?php elseif($gym_statement->rowCount() > 0): ?>
                <?php $row = $gym_statement->fetch() ?>
                <h1>Title: <?= $row['post_title'] ?></h1>
                <p>Date Created: <?= $row['post_date'] ?></p>
                <p>Discussion Topic: Gym Leader - <?= $row['gym_leader'] ?> - <?= $row['gym_description'] ?></p>
                <p>Blog Content: <?= $row['post_desc'] ?></p>
                <?php if(isset($_SESSION['current_user'])): ?>
                    <?php if($_SESSION['current_user_id'] == $row['user_id'] || $_SESSION['current_user_admin'] == 1): ?>
                    <p><a class="editlink" href="edit.php?id=<?= $row['blog_id'] ?>">Edit Post</a></p>
                    <?php endif ?>
                <?php endif ?>
            <?php elseif($location_statement->rowCount() > 0): ?>
                <?php $row = $location_statement->fetch() ?>
                <h1>Title: <?= $row['post_title'] ?></h1>
                <p>Date Created: <?= $row['post_date'] ?></p>
                <p>Discussion Topic: <?= $row['location_name'] ?> - <?= $row['location_description'] ?></p>
                <p>Blog Content: <?= $row['post_desc'] ?></p>
                <?php if(isset($_SESSION['current_user'])): ?>
                    <?php if($_SESSION['current_user_id'] == $row['user_id'] || $_SESSION['current_user_admin'] == 1): ?>
                    <p><a class="editlink" href="edit.php?id=<?= $row['blog_id'] ?>">Edit Post</a></p>
                    <?php endif ?>
                <?php endif ?>
            <?php else: ?>
                <h1>The page you requested could not be found.</h1>
            <?php endif ?>
        </div>
    </article>

</body>
</html>
