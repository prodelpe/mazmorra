<?php

session_start(); //Iniciem sessió
include('../class/connect.php'); //Connectem amb la BD
include('../class/user.php'); //Cridem a la classe user per crear l'user
$user = new User($dbConnection); //Creem nou usuari amb la connecxío (hem agafat la connexió de la classe User)
//Si s'ha carregat un arxiu...
if ($_FILES['imagen']['size'] > 0) {

    //Comprovem si és una imatge
    $imageMimeTypes = array(
        'image/png',
        'image/gif',
        'image/jpeg');

    $fileMimeType = mime_content_type($_FILES['imagen']['tmp_name']);

    if (in_array($fileMimeType, $imageMimeTypes)) {//Si és una imatge...
        $nombre = $_FILES['imagen']['name']; //Agafem el nom de la imatge
        $nombrer = strtolower($nombre); //El passem a lowercase
        $ruta = "../img/" . $nombrer; //Posem la ruta on anira a parar l'arxiu (inclós el nom de l'arxiu)
        $resultado = @move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta); //Pugem l'arxiu al lloc indicat           
        $ruta = substr($ruta, 3); //Corregim la ruta perque sigui vista des de my-data.php (esborrem ../)
    } else {//Si no es una imatge...
        header("Location: ../my-data.php?error=0&message=El archivo tiene un formato no permitido");
    }
} else {//Si no s'ha carregat l'arxiu, mantenim el que ja hi havia
    $ruta = $user->getUserPicture($_SESSION['email']);
    $ruta = $ruta['imageUser']['image'];
}

//Carregem les dades de l'usuari per si els necessitem
$userData = $user->getUserData($_SESSION['email']);

if (strlen($_POST['nickname']) <= 0) {
    $nickname = $userData['nickname'];
} else {
    $nickname = $_POST['nickname'];
    $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_STRING);
}

if (strlen($_POST['bio']) <= 0) {
    $bio = $userData['bio'];
} else {
    $bio = $_POST['bio'];
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
}

//I ara ja podem pujar l'arxiu
$updateData = $user->updateData($_SESSION['email'], $nickname, $bio, $ruta);

//echo $_SESSION['email'], $nickname, $bio, $ruta;

header("Location: ../my-data.php?error=1&message=Datos actualizados");
?>