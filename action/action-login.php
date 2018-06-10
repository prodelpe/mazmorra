<?php

if (isset($_POST['enviar']) and isset($_POST['inputEmail']) and isset($_POST['inputPassword'])) {

    //Validem email... No se fins a quin punt necessari si ja valida HTML5
    if (filter_input(INPUT_POST, "inputEmail", FILTER_VALIDATE_EMAIL)) {

        session_start(); //Iniciem sessió
        include('../class/connect.php'); //Connectem amb la BD
        include('../class/user.php'); //Cridem a la classe user per crear l'user
        $user = new User($dbConnection); //Creem nou usuari amb la connecxío (hem agafat la connexió de la classe User)

        $formMail = filter_input(INPUT_POST, 'inputEmail', FILTER_SANITIZE_EMAIL); // Netejem l'email

        $login = $user->login($formMail, $_POST['inputPassword']);

        //secho var_dump($login);

        //Si troba un error retorna a la web de login
        if ($login['error'] == 1) {
            header("Location: ../login.php?error=1&message=" . $login['message']);
        } else {
            header("Location: ../my-account.php");
        }
    } else {
        // Error en dades enviades	
        header("Location: ../login.php?error=1&message=Hay un error en los datos enviados");
    }
} else {
    // Error al formulari (en teoria mai sortirà gràcies a HTML5)
    header("Location: ../login.php?error=1&message=Debes llenar todos los campos" . $_POST['inputEmail']);
}
