<?php
include('../class/connect.php'); //Connectem amb la BD
include '../class/review.php';
include '../class/ChromePhp.php';

$review = new Review($dbConnection);

$userId = $_GET['userId'];
$bookId = $_GET['bookId'];
$title = $_GET['titulo'];
$comment = $_GET['critica'];
$rating = $_GET['rating'];

$a = $review->setReview($userId, $bookId, $title, $comment, $rating);

if($a!=0){
    header('Location: ../my-biblio.php');
} else {
    echo "Error añadiendo los datos";
    exit();
}
