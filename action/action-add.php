<?php

include('../class/connect.php'); //Connectem amb la BD
include('../class/book.php'); //Connectem amb la BD
include '../class/ChromePhp.php';
$book = new Book($dbConnection);

$userId = $_POST['userId'];
$bookId = $_POST['bookId'];

try {
    //Afegim les dades del llibre a la base de dades i retornem el seu número de id a la nostra BD
    $a = $book->saveBook($userId, $bookId);
    //ChromePhp::log($a);
    
//    //Preparem el rediriccionament:
//    $url = '../my-ficha.php?idAPI=' . $bookId;
//    
//    //Costruim la url amb paràmetres
//    $params = http_build_query([
//            'wasInBD' => $a['wasInBD'],
//            'wasInFavorites' => $a['wasInFavorites']
//        ]);
//    
//    $url = $url . $params;
//    
//    ChromePhp::log($url);
//    
//    header('Location : mazmorra/my-ficha.php?idAPI=oz3ZDgAAQBAJ');

} catch (Exception $e) { // Si hi ha algun error en el procès, retornem l'error al JSON

    echo 'Se ha producido un error';
    echo json_encode($jsondata);
    exit();
}
