<?php

class User {

    private $dbConnection;
    private $encryptKey;

    /**
     * Creem un objecte user
     * @param type $dbConnection
     */
    function __construct($dbConnection) {
        //Iniciem una connexió amb la BD
        $this->dbConnection = $dbConnection;
        //Li donem un valor al codi de encriptació
        $this->encryptKey = 'rv76hut-gyjjK9fghlHR47hskd62HG))%4ggk';

        // Si la versió de php on ho fem funcionar es anterior a la 5.5.0 afegim 
        // la classe password per afegir compatibilitat amb les noves funcionalitats d'encriptació
        if (version_compare(phpversion(), '5.5.0', '<')) {
            require("password.php");
        }
    }

    /**
     * Permet la entrada d'un usuari a la web i obre la sessió
     * @param type $email
     * @param type $password
     * @return array
     */
    public function login($email, $password) {

        $return = array();
        $user = $this->getUserData($email); //Extreiem l'array resultat de getUserData

        if ($user['userfound']) {//Si exiteix l'email vol dir que l'usuari existeix
            $return['password'] = $password;
            $return['password_hash'] = $user['password'];
            //Ara lo normal seria jo buscar a la taula si el password existeix... Decriptar el password i mirar si coincideix...
            //Però sembla que PHP 5.5 ja porta la funció incorporada...
            //
            // Es verifica doncs el password de l'usuari amb les funcions de verificació de php 5.5.0 (o la classe de compatibilitat)
            if (password_verify($password, $user['password'])) {//Si funciona...
                // 
                $_SESSION['id'] = $user['id'];
                $_SESSION['nickname'] = $user['nickname'];
                $_SESSION['hash'] = sha1($user['id'] . $this->encryptKey);
                $_SESSION['email'] = $user['email'];
                $_SESSION['bio'] = $user['bio'];
                //Iniciem també les variables que encara no estàn creades
                $_SESSION['image'] = $user['image'];
                $return['error'] = 0;
                return $return;
            } else {
                $return['error'] = 1;
                $return['message'] = "Contraseña incorrecta";
                return $return;
            }
        }

        return $return;
    }

    /**
     * Registre d'un usuari a la web
     * @param type $email
     * @param type $password
     * @return array
     */
    public function register($email, $nickname, $password) {

        $return = array();

        //Tornem a chequejar que les dades siguin correctes... Si no retornarem error
        //Primer comprovem que tingui un número de caràcters que no sigui absurd
        if (strlen($email) == 0 or strlen($email) < 3 or strlen($email) > 150) {
            $return['error'] = 1;
            $return['message'] = "Email no vàlid";
            return $return;
            //Fem servir filter_var que filtra una variable amb el filtre que se l'indiqui
            //En aquest cas filter_var
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return['error'] = 1;
            $return['message'] = "Email no vàlid";
            return $return;
            //Comprovem longutud del nickname amb strlen
        } elseif (strlen($nickname) == 0 or strlen($nickname) < 3 or strlen($nickname) > 150) {
            $return['error'] = 1;
            $return['message'] = "Nickname no vàlid";
            return $return;
            //I lo mateix amb la del password
        } elseif (strlen($password) < 3 or strlen($password) > 60) {
            $return['error'] = 1;
            $return['message'] = "Password no vàlid";
            return $return;
        } else { //Si les dades son vàlides passem a la següent fase...
            if (!$this->checkUserEmail($email)) {//Si l'email no est'a repetit
                // Netejem dades HTML
                $nickname = htmlentities($nickname);
                $email = htmlentities($email);
                //Encriptem el password
                $salt = substr(strtr(base64_encode(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)), '+', '.'), 0, 22);
                $bcrypt_cost = 10;
                $password = password_hash($password, PASSWORD_BCRYPT, ['salt' => $salt, 'cost' => $bcrypt_cost]);
                //Fem la query per introduïr l'user
                $query = $this->dbConnection->prepare("INSERT INTO users (email, password, nickname) VALUES (?, ?, ?)");
                //La executem (execute sempre ha de portar un array)
                $query->execute(array($email, $password, $nickname));
                $return['error'] = 0; //Retornme 0 errors
                //Retornem l'id de l'usuari que correspon al de la taula assenyalat com primary_index
                $return['id'] = $this->dbConnection->lastInsertId();
                $return['email'] = $email;
                //Iniciem la sessió! S'assumeix que l'User ja està registrat
                $_SESSION['nickname'] = $nickname;
                $_SESSION['id'] = $return['id'];
                //Iniciem també les variables que encara no estàn creades
                $_SESSION['bio'] = NULL;
                $_SESSION['image'] = NULL;
                //NOTA: hash no se que era
                $_SESSION['hash'] = sha1($return['id'] . $this->encryptKey);
                return $return;
            } else {
                $return['error'] = 1; //Primer assegurem que hi ha un error
                $return['message'] = "Email ya registrado"; //Missatge d'error
            }
        }

        return $return;
    }

    /**
     * Comprova si un email ja està registrat (en principi deixem que existeixi el nom dues vegades)
     * @param type $email
     */
    public function checkUserEmail($email) {
        $query = $this->dbConnection->prepare("SELECT email FROM `users` WHERE email = ?"); //Fem la tàctica de l'interrogant però podríem posar la variable
        $query->execute(array($email)); //PDO sempre dins d'un array!

        if ($query->rowCount() == 0) {//Si retorna 0 files és que no l'ha trobat
            return false;
        } else {
            return true;
        }
    }

    /**
     * Cerca les dades d'un usuari a partir del seu email
     * Serveix per exemple per fer el login... Primer comprovem que l'usuari existeix
     * @param type $email
     */
    public function getUserData($email) {
        $return = array();
        //Preparem la query
        $query = $this->dbConnection->prepare("SELECT * FROM users WHERE email = ?");
        //La executem (PDO sempre demana array(?))
        $query->execute(array($email));
        //Si retorna files... és que no hi és!
        if ($query->rowCount() == 0) {
            $return['userfound'] = false;
            return $return;
        } else {//Si retorna files, vol dir que existeix
            $return = $query->fetch(PDO::FETCH_ASSOC); //Guardem el resultat de la query! (totes les dades de l'user)
            $return['userfound'] = true;
            return $return;
        }
    }

    /**
     *
     * Funció per verificar si existeix una sessió d'usuari activa i correcte
     *
     * @return boolean Retorna true o false en funció de la verificació de la sessió
     */
    public function checkUserSession() {
        // Si existeisen els parametres a sessió..
        if (isset($_SESSION['id']) && isset($_SESSION['hash'])) {
            // Si a més a més, els parametres encaixen amb el criteri de creació de sessió
            if ($_SESSION['hash'] == sha1($_SESSION['id'] . $this->encryptKey)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Comprova si l'usuari te una fotografia de perfil
     * @param type $email
     */
    public function getUserPicture($email) {
        $return = array();
        $query = $this->dbConnection->prepare("SELECT image FROM users WHERE email = ?");
        $query->execute(array($email));
        if ($query->rowCount() == 0) {
            $return['imgfound'] = false;
            return $return;
        } else {//Se suposa que només trobarà una fila
            $return['imgfound'] = true;
            $return['imageUser'] = $query->fetch(PDO::FETCH_ASSOC);
            return $return;
        }

        return $return;
    }

    /**
     * Actualitza les dades d'un usuari des del formulari de action-update
     * @param type $email
     * @param type $nickname
     * @param type $bio
     * @param type $image
     * @return array
     */
    public function updateData($email, $nickname, $bio, $image) {

        $return = array();

        $query = $this->dbConnection->prepare("UPDATE users SET nickname = ?, bio = ?, image = ? WHERE email = ?");
        $query->execute(array($nickname, $bio, $image, $email));

        //Actualitzem sessió (TODO: no funciona?)
        $_SESSION['nickname'] = $this->getUserData($email)['nickname'];
        $_SESSION['bio'] = $this->getUserData($email)['bio'];
        $_SESSION['image'] = $this->getUserData($email)['image'];

        return $return;
    }

    /**
     * Retorna l'usuari més actiu per mostrar-lo a la home
     */
    function getMostActiveUser() {
        $query = $this->dbConnection->prepare("SELECT sum(r.id) AS totalReviews, r.userId, u.id, u.nickname, u.bio, u.image FROM reviews r LEFT JOIN users u ON r.userId = u.id GROUP BY userId DESC LIMIT 1");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

}
