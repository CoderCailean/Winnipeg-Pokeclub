<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 * This page allows users to create new blog posts. Data is validated/sanitized, then added to the database.
 *
 */
require('connect.php');

session_start();

date_default_timezone_set('America/Winnipeg');

function file_upload_path($original_filename, $upload_subfolder_name = 'images')
{
    $current_folder = dirname(__FILE__);

    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

    return join(DIRECTORY_SEPARATOR, $path_segments);
}

function file_is_valid($temporary_path, $new_path)
{
    $allowed_mime_types = ['image/jpeg', 'image/gif', 'image/png'];
    $allowed_file_extensions = ['jpg', 'jpeg', 'gif', 'png'];

    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = mime_content_type($temporary_path);

    $file_extension_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_content_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_valid && $mime_content_valid;
}


$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

$file_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

if($image_upload_detected)
{
    $image_filename = $_FILES['image']['name'];
    $temp_image_path = $_FILES['image']['tmp_name'];
    $new_image_path = file_upload_path($image_filename);
    if(file_is_valid($temp_image_path, $new_image_path))
    {

        move_uploaded_file($temp_image_path, $new_image_path);

    }
}


$pokemon_query = "SELECT pokemon_id, pokemon_name FROM pokemon ORDER BY pokemon_id ASC";
$pokemonstatement = $db->prepare($pokemon_query);
$pokemonstatement->execute();

$location_query = "SELECT location_id, location_name FROM location ORDER BY location_id ASC";
$locationstatement = $db->prepare($location_query);
$locationstatement->execute();

$gym_query = "SELECT g.gym_id, l.location_name FROM gym g JOIN location l ON g.location_id = l.location_id ORDER BY gym_id ASC";
$gymstatement = $db->prepare($gym_query);
$gymstatement->execute();


$errors = false;
$selection_error = false;
$too_many_selections_error = false;


if(isset($_POST['title']) && isset($_POST['content']))
{
    if($_POST['pokemonlist'] > 0 && $_POST['locationlist'] > 0 && $_POST['gymlist'] > 0)
    {
        $too_many_selections_error = true;

        if($_POST['title'] > 1 && $_POST['content'] > 1)
        {
            
        }
        else
        {
            $errors = true;
        }
    }
    elseif($_POST['pokemonlist'] > 0 && $_POST['locationlist'] > 0 && $_POST['gymlist'] == 0)
    {
        $too_many_selections_error = true;

        if($_POST['title'] > 1 && $_POST['content'] > 1)
        {

        }
        else
        {
            $errors = true;
        }
    }
    elseif($_POST['pokemonlist'] == 0 && $_POST['locationlist'] > 0 && $_POST['gymlist'] > 0)
    {
        $too_many_selections_error = true;

        if($_POST['title'] > 1 && $_POST['content'] > 1)
        {

        }
        else
        {
            $errors = true;
        }
    }
    elseif($_POST['pokemonlist'] > 0 && $_POST['locationlist'] == 0 && $_POST['gymlist'] > 0)
    {
        $too_many_selections_error = true;

        if($_POST['title'] > 1 && $_POST['content'] > 1)
        {

        }
        else
        {
            $errors = true;
        }
    }
    elseif($_POST['pokemonlist'] > 0 && $_POST['locationlist'] == 0 && $_POST['gymlist'] == 0)
    {
        if(strlen($_POST['title']) > 1 && strlen($_POST['content']) > 1)
        {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $date = date("Y-m-d H:i:s");
            $pokemon_id = filter_input(INPUT_POST, 'pokemonlist', FILTER_SANITIZE_NUMBER_INT);


            $insert_query = "INSERT INTO post (post_title, post_desc, post_date, user_id, pokemon_id, image_file) 
                             VALUES (:title, :description, :created, :user, :pokemon, :image)";

            $insert_statement = $db->prepare($insert_query);
            $insert_statement->bindValue(':title', $title);
            $insert_statement->bindValue(':description', $content);
            $insert_statement->bindValue(':created', $date);
            $insert_statement->bindValue(':user', $_SESSION['current_user_id']);
            $insert_statement->bindValue(':pokemon', $pokemon_id);
            $insert_statement->bindValue(':image', $_FILES['image']['name']);
            $insert_statement->execute();

            header('Location: index.php');
        }
        else
        {
            $errors = true;
        }
    }
    elseif($_POST['pokemonlist'] == 0 && $_POST['locationlist'] > 0 && $_POST['gymlist'] == 0)
    {
        if(strlen($_POST['title']) > 1 && strlen($_POST['content']) > 1)
        {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $date = date("Y-m-d H:i:s");
            $location_id = filter_input(INPUT_POST, 'locationlist', FILTER_SANITIZE_NUMBER_INT);


            $insert_query = "INSERT INTO post (post_title, post_desc, post_date, user_id, location_id, image_file) 
                             VALUES (:title, :description, :created, :user, :location, :image)";

            $insert_statement = $db->prepare($insert_query);
            $insert_statement->bindValue(':title', $title);
            $insert_statement->bindValue(':description', $content);
            $insert_statement->bindValue(':created', $date);
            $insert_statement->bindValue(':user', $user_id);
            $insert_statement->bindValue(':location', $location_id);
            $insert_statement->bindValue(':image', $_FILES['image']['name']);
            $insert_statement->execute();

            header('Location: index.php');
        }
        else
        {
            $errors = true;
        }
    }
    elseif($_POST['pokemonlist'] == 0 && $_POST['locationlist'] == 0 && $_POST['gymlist'] > 0)
    {
        if(strlen($_POST['title']) > 1 && strlen($_POST['content']) > 1)
        {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $date = date("Y-m-d H:i:s");
            $gym_id = filter_input(INPUT_POST, 'gymlist', FILTER_SANITIZE_NUMBER_INT);


            $insert_query = "INSERT INTO post (post_title, post_desc, post_date, user_id, gym_id, image_file) 
                             VALUES (:title, :description, :created, :user, :gym, :image)";

            $insert_statement = $db->prepare($insert_query);
            $insert_statement->bindValue(':title', $title);
            $insert_statement->bindValue(':description', $content);
            $insert_statement->bindValue(':created', $date);
            $insert_statement->bindValue(':user', $user_id);
            $insert_statement->bindValue(':gym', $gym_id);
            $insert_statement->bindValue(':image', $_FILES['image']['name']);
            $insert_statement->execute();

            header('Location: index.php');
        }
        else
        {
            $errors = true;
        }
    }
    else
    {
        $selection_error = true;
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
	<title>Create a Post!</title>
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
    <?php if(isset($_SESSION['current_user'])): ?>
    <form method="post" action="create.php" enctype="multipart/form-data">
        <label for="title">Post Title:</label>
        <input type="text" name="title">
        <?php if($errors): ?>
            <p>*Must contain 1 or more characters.</p>
        <?php endif ?>
        <label>Choose a Pokemon!</label>
        <select id="pokemonlist" name="pokemonlist">
            <option value="0">--Pokemon--</option>
            <?php for($i = 0; $i < $pokemonstatement->rowCount(); $i++): ?>
                <?php $row = $pokemonstatement->fetch() ?>
                <option value="<?= $row['pokemon_id'] ?>"><?= $row['pokemon_name'] ?></option>
            <?php endfor ?>
        </select>
        <?php if($selection_error): ?>
            <p>*Please make a selection.</p>
        <?php endif ?>
        <?php if($too_many_selections_error): ?>
            <p>*Please select either a Pokemon, Location or Gym.</p>
        <?php endif ?>
        <label>Or</label>
        <select id="locationlist" name="locationlist">
            <option value="0">--Locations--</option>
            <?php for($i = 0; $i < $locationstatement->rowCount(); $i++): ?>
                <?php $row = $locationstatement->fetch() ?>
                <option value="<?= $row['location_id'] ?>"><?= $row['location_name'] ?></option>
            <?php endfor ?>
        </select>
        <?php if($selection_error): ?>
            <p>*Please make a selection.</p>
        <?php endif ?>
        <?php if($too_many_selections_error): ?>
            <p>*Please select either a Pokemon, Location or Gym.</p>
        <?php endif ?>
        <label>Or</label>
        <select id="gymlist" name="gymlist">
            <option value="0">--Gyms--</option>
            <?php for($i = 0; $i < $gymstatement->rowCount(); $i++): ?>
                <?php $row = $gymstatement->fetch() ?>
                <option value="<?= $row['gym_id'] ?>"><?= $row['location_name'] ?> Gym</option>
            <?php endfor ?>
        </select>
        <?php if($selection_error): ?>
            <p>*Please make a selection.</p>
        <?php endif ?>
        <?php if($too_many_selections_error): ?>
            <p>*Please select either a Pokemon, Location or Gym.</p>
        <?php endif ?>
        <label for="content">Blog Content:</label>
        <textarea name="content" placeholder="New Blog Post..."></textarea>
        <?php if($errors): ?>
            <p>*Must contain 1 or more characters.</p>
        <?php endif ?>
        <label for="image">Image (optional):</label>
        <input type="file" name="image">
        <input type="submit" name="submit" value="Create Post" />
    </form>
    <?php else: ?>
        <div class="login_redirect">
            <p>To create a new post, please <a href="login.php">login</a>.</p>
        </div>
    <?php endif ?>
</body>
</html>