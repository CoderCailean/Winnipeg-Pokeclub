<?php 
/*
 * Cailean Horton
 * Web Development 2 - Project
 *
 * This script provides a connection to the serverside database.
 */

define('DB_DSN','mysql:host=localhost;dbname=serverside');
define('DB_USER','serveruser');
define('DB_PASS','gorgonzola7!');

try {
    $db = new PDO(DB_DSN, DB_USER, DB_PASS);
} catch (PDOException $e) {
    print "Error: " . $e->getMessage();
    die(); // Force execution to stop on errors.
}


?>