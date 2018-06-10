<?php
include('../class/connect.php');
include('../class/book.php');
$book = new Book($dbConnection);
$book->deleteFavorite($_GET['id']);
header("Location: ../my-biblio.php");
?>

