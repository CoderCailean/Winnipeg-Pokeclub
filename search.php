<?php 
require('connect.php');

session_start();

$ready_to_search = false;
$ready_to_search_blogs = false;

if(isset($_GET['searchquery']))
{
	$userinput = filter_input(INPUT_GET, 'searchquery', FILTER_SANITIZE_SPECIAL_CHARS);

	$ready_to_search_blogs = true;

	$blog_title_search = "SELECT * FROM post WHERE post_title LIKE CONCAT('%', :usersearch, '%') OR post_desc LIKE CONCAT('%', :usersearch, '%')";

	$title_statement = $db->prepare($blog_title_search);
	$title_statement->bindValue(':usersearch', $userinput);
	$title_statement->execute();
}


if(isset($_POST['searchbar']))
{
	$ready_to_search = true;

	$userinput = filter_input(INPUT_POST, 'searchbar', FILTER_SANITIZE_SPECIAL_CHARS);

	$name_search_query = "SELECT p.pokemon_name, p.pokemon_desc, p.pokemon_id, c.ct_name FROM pokemon p 
						  JOIN category_type c ON p.ct_id_pokemon_type = c.ct_id WHERE pokemon_name LIKE CONCAT('%', :usersearch, '%')";
	$name_statement = $db->prepare($name_search_query);
	$name_statement->bindValue(':usersearch', $userinput);
	$name_statement->execute();

	$type_search_query = "SELECT p.pokemon_name, p.pokemon_desc, p.pokemon_id, c.ct_name FROM pokemon p 
						  JOIN category_type c ON p.ct_id_pokemon_type = c.ct_id WHERE c.ct_name LIKE CONCAT('%', :usersearch, '%')";
	$type_statement = $db->prepare($type_search_query);
	$type_statement->bindValue(':usersearch', $userinput);
	$type_statement->execute();
}

if(isset($_POST['blogsearchbar']))
{
	$ready_to_search_blogs = true;

	$userinput = filter_input(INPUT_POST, 'blogsearchbar', FILTER_SANITIZE_SPECIAL_CHARS);

	$blog_title_search = "SELECT * FROM post WHERE post_title LIKE CONCAT('%', :usersearch, '%') OR post_desc LIKE CONCAT('%', :usersearch, '%')";

	$title_statement = $db->prepare($blog_title_search);
	$title_statement->bindValue(':usersearch', $userinput);
	$title_statement->execute();
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
	<title>Search</title>
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
	<form method="post">
		<label>Search for Generation 1 Pokemon by Name or Type:</label>
		<input type="text" name="searchbar">
		<input type="submit" value="Submit Search">
	</form>
	<div id="results">
		<?php if($ready_to_search): ?>
		<?php if($name_statement->rowCount() > 0): ?>
			<?php for($i = 0; $i < $name_statement->rowCount(); $i++): ?>
				<?php $row = $name_statement->fetch() ?>
				<div class="pokemonresults">
					<h1>#<?= $row['pokemon_id'] ?> - <?= $row['pokemon_name'] ?></h1>
					<h1>Type: <?= $row['ct_name'] ?></h1>
					<p><?= $row['pokemon_desc'] ?></p>
				</div>
			<?php endfor ?>
		<?php elseif($type_statement->rowCount() > 0): ?>
			<?php for($i = 0; $i < $type_statement->rowCount(); $i++): ?>
				<?php $row = $type_statement->fetch() ?>
				<div class="pokemonresults">
					<h1>#<?= $row['pokemon_id'] ?> - <?= $row['pokemon_name'] ?></h1>
					<h1>Type: <?= $row['ct_name'] ?></h1>
					<p><?= $row['pokemon_desc'] ?></p>
				</div>
			<?php endfor ?>
		<?php else: ?>
		<h1>Your search returned 0 entries.</h1>
		<?php endif ?>
		<?php endif ?>
	</div>
	<form id="blogsearch" method="post">
		<label>Search Blog posts containing key words:</label>
		<input type="text" name="blogsearchbar">
		<input type="submit" value="Submit Search">
	</form>
	<div id="blogresults">
		<?php if($ready_to_search_blogs): ?>
			<?php if($title_statement->rowCount() > 0): ?>
				<?php $blog = $title_statement->fetchAll() ?>
				<?php foreach($blog AS $blog_post): ?>
				<div>
					<p><a href="read.php?id=<?= $blog_post['blog_id'] ?>"><?= $blog_post['post_title'] ?></a></p>
				</div>
				<?php endforeach ?>
			<?php else: ?>
				<h1>Your search returned 0 results.</h1>
			<?php endif ?>
		<?php endif ?>
	</div>

</body>
</html>