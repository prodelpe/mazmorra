<?php

/**
 * Comprovem primer de tot que s'han omplert els camps del formulari
 */
if (isset($_POST['enviar'])
        and isset($_POST['inputEmail'])
        and isset($_POST['inputNickname'])
        and isset($_POST['inputPassword'])
        and isset($_POST['conditions'])) {

    //Validem email... No se fins a quin punt necessari si ja valida HTML5
    if (filter_input(INPUT_POST, "inputEmail", FILTER_VALIDATE_EMAIL)) {

        session_start(); //Iniciem sessió
        include('../class/connect.php'); //Connectem amb la BD
        include('../class/user.php'); //Cridem a la classe user per crear l'user
        $user = new User($dbConnection); //Creem nou usuari amb la connecxío (hem agafat la connexió de la classe User)

        /**
         * Netejem les dades amb funcions prefefinides de PHP
         */
        $formMail = filter_input(INPUT_POST, 'inputEmail', FILTER_SANITIZE_EMAIL); // Netejem l'email
        $formName = filter_input(INPUT_POST, 'inputNickname', FILTER_SANITIZE_STRING); // Netejem el nom d'usuari
        /**
         * El password no el netejem perque es farà un hash amb ell i no el volem modificar
         * Registrem l'usuari via funció redister de la classe User
         */
        $register = $user->register($formMail, $formName, $_POST['inputPassword']);
        //
        if ($register['error'] == 0) { // Usuari registrat correctament
            header("Location: ../my-account.php?newuser=1"); // Redireccionem amb GET i un paràmetre
        } else { // Error al registrar al usuari, tornem i mostrem l'error
            header("Location: ../register.php?error=1&message=" . $register['message']); //Enviem els errors
        }
    } else {// Error en dades enviades	
        header("Location: register.php?error=1&message=Hi ha errors en les dades enviades");
    }
} else {// Error al formulari (en teoria mái sortirà gràcies a HTML5)
    header("Location: register.php?error=1&message=Tots els camps han de ser emplenats i acceptar les condicions legals");
}

