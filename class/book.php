<?php

//Clave API
//AIzaSyBJq-jx7P20-bftFgKYwRXclKUCKzyGvE8
//
//https://developers.google.com/books/docs/v1/using
//
//Ejemplo: https://www.googleapis.com/books/v1/volumes?q=asimov

class Book {

    private $dbConnection;
    private $API_KEY = 'AIzaSyBJq-jx7P20-bftFgKYwRXclKUCKzyGvE8';

    function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Busca llibres a la BD pròpia
     * @param type $string
     * @return type
     */
    public function searchBooks($string) {
        $query = $this->dbConnection->prepare('SELECT * FROM books WHERE name LIKE ?');
        $query->execute(array("%$string%")); //Que contingui la string pero pot tenir el que sigui per davant i per redera
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchInAPI($string, $index) {

        $return = array();

        //Definim els paràmetres que farem servir
        $queryParams = http_build_query([//Ja posa els & automàtics
            'q' => $string, //Busca a tot arreu: titol, autor, descripció...
            'maxResults' => '30', //13 files
            'startIndex' => $index, //Per la paginació!!
            'key' => $this->API_KEY
        ]);

        // Contruim la url a on farem la crida
        //Per defecte només es mostren 10 resultats... però podem demanar més afegint més paràmetres
        //https://stackoverflow.com/questions/11375173/google-books-api-returns-only-10-results
        $url = 'https://www.googleapis.com/books/v1/volumes?' . $queryParams;

        // Iniciem curl per fer la petició
        $ch = curl_init();

        //ESTABLIM OPCIONS -> CURL-SETOPT — Configura una opción para una transferencia cURL
        // Deshabilitem SSL perque l'api no ho fa servir
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Per obtenir el retorn de la resposta
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Li indiquem la URL on fer la petició
        curl_setopt($ch, CURLOPT_URL, $url);

        //EXCEUTEM PETICIÓ
        // Executem la petició
        $APIresult = curl_exec($ch);

        // Tanquem la connexió
        curl_close($ch);

        // Decodifiquem el JSON rebut
        $JSONresult = json_decode($APIresult, true);

        // Si troba resultats els tractem abans de retornar
        if (isset($JSONresult['items'])) {
            foreach ($JSONresult['items'] as $book) {
                //Predefinimos
                $tmpBook = array();

                //Solo cogemos en castellano
                if ($book['volumeInfo']['language'] == 'es') {//Y cogemos lo que nos interesa...
                    $tmpBook['idAPI'] = isset($book['id']) ? $book['id'] : NULL;
                    $tmpBook['title'] = isset($book['volumeInfo']['title']) ? $book['volumeInfo']['title'] : NULL;
                    $tmpBook['authors'] = isset($book['volumeInfo']['authors']) ? implode(",", $book['volumeInfo']['authors']) : NULL;
                    $tmpBook['categories'] = isset($book['volumeInfo']['categories']) ? implode(",", $book['volumeInfo']['categories']) : NULL;
                    $tmpBook['description'] = isset($book['volumeInfo']['description']) ? $book['volumeInfo']['description'] : NULL;
                    $tmpBook['publisher'] = isset($book['volumeInfo']['publisher']) ? $book['volumeInfo']['publisher'] : NULL;
                    $tmpBook['image'] = isset($book['volumeInfo']['imageLinks']['thumbnail']) ? $book['volumeInfo']['imageLinks']['thumbnail'] : 'img/books/generic-cover.jpg';
                    $tmpBook['textSnippet'] = isset($book['searchInfo']['textSnippet']) ? $book['searchInfo']['textSnippet'] : NULL; //Mal... en realidad no existe el textSnippet en la ficha individial...
                }

                if (!empty($tmpBook)) {
                    array_push($return, $tmpBook);
                }
            }
        } else {
            $return['error'] = 1;
            $return['message'] = 'No se han encontrado resultados';
        }

        return $return;
    }

    /**
     * Retorna un sol register després de buscar a la API
     * @param type $string
     */
    public function SingleBookInAPI($string) {

        $return = NULL;

        $url = 'https://www.googleapis.com/books/v1/volumes/' . $string . '?key=' . $this->API_KEY;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $APIresult = curl_exec($ch);
        curl_close($ch);

        $JSONresult = json_decode($APIresult, true);

        if (isset($JSONresult['id']) && $JSONresult['id'] == $string) {
            $return['id'] = $JSONresult['id']; //El necessitem per la petició Ajax
            $return['title'] = isset($JSONresult['volumeInfo']['title']) ? $JSONresult['volumeInfo']['title'] : NULL;
            $return['authors'] = isset($JSONresult['volumeInfo']['authors']) ? implode(", ", $JSONresult['volumeInfo']['authors']) : NULL;
            $return['categories'] = isset($JSONresult['volumeInfo']['categories']) ? implode(", ", $JSONresult['volumeInfo']['categories']) : NULL;
            $return['description'] = isset($JSONresult['volumeInfo']['description']) ? $JSONresult['volumeInfo']['description'] : NULL;
            $return['publisher'] = isset($JSONresult['volumeInfo']['publisher']) ? $JSONresult['volumeInfo']['publisher'] : NULL;
            $return['image'] = isset($JSONresult['volumeInfo']['imageLinks']['thumbnail']) ? $JSONresult['volumeInfo']['imageLinks']['thumbnail'] : 'img/books/generic-cover.jpg';
            $return['pageCount'] = isset($JSONresult['volumeInfo']['pageCount']) ? $JSONresult['volumeInfo']['pageCount'] : NULL;
            $return['textSnippet'] = isset($JSONresult['searchInfo']['textSnippet']) ? $JSONresult['searchInfo']['textSnippet'] : NULL;
            $return['description'] = isset($JSONresult['volumeInfo']['description']) ? $JSONresult['volumeInfo']['description'] : NULL;
        } else {
            $return['error'] = 1;
            $return['message'] = "Ha habido un error al buscar los datos";
        }

        return $return;
    }

    /**
     * Guarda llibre a la BD local i l'afegeix com a favorit
     * @param type $id
     */
    public function saveBook($userId, $bookId) {

        $return = array();

        //Primer treiem la info del book que volem guardar a la BD
        $book = $this->SingleBookInAPI($bookId);

        if ($this->checkBookInBooks($book['id']) == 0) {//Si el llibre no existeix a la base de dades...
            //Guardem la imatge al servidor
            //https://stackoverflow.com/questions/26560263/php-getting-cover-image-from-google-books-api
            $imagedata = file_get_contents($book['image']);
            //Li donem nom aleatori a la imatge
            $imageName = $this->randomString(10) . '.jpg';
            //Creem la ruta (contem que les imatges es veuren des del directori principal)
            $ruta = '../img/books/' . $imageName;

            //Creem la ruta
            file_put_contents($ruta, $imagedata);

            // just to see it
            $img = imagecreatefromstring($imagedata);
            header('Content-Type: image/jpg');
            imagejpeg($img);
            imagedestroy($img);

            //Primer guardem el llibre a la taula books de la BD local
            //(No se jo si és molt bona idea fer-ho així, poder seria més cómode manejar sempre la API... però per practicar)
            $insertBook = $this->dbConnection->prepare("INSERT INTO books (idAPI, title, authors, image, textSnippet, description) VALUES (?, ?, ?, ?, ?, ?)");
            $insertBook->execute(array($book['id'], $book['title'], $book['authors'], substr($ruta, 3), $book['textSnippet'], $book['description']));
            //Agafem l'id d'aquest llibre
            $bookIdForBD = $this->dbConnection->lastInsertId();
            $return['wasInBD'] = false; //o 0
        } else {
            //Si el llibre ja el teniem a la base de dades, pillem el seu id
            $return['wasInBD'] = true; //o 1
            $bookIdForBD = $this->checkBookInBooks($book['id']);
        }

        //Ara procedim a guardar com a favorit
        //Igualmentm, només es podrà fer si ja no el té afegit
        if ($this->checkBookInUserBooks($bookIdForBD) == 0) {
            $insertUserBooks = $this->dbConnection->prepare("INSERT INTO userbooks (userId, bookId) VALUES (?, ?)");
            $insertUserBooks->execute(array($userId, $bookIdForBD));
            $return['wasInFavorites'] = false; //o 0
            //Ara comprovem si ha estat afegit
            if ($insertUserBooks->errorCode() == 0) { // Si no hi ha cap problema, retornem l'identificador de la rel.lació
                //Guardo el return original, crec que no faria falta...
                $return['originalReturn'] = $this->dbConnection->lastInsertId();
            } else {
                $return['originalReturn'] = 0; // Si hi ha algun error en el procès retornem 0
            }
        } else {
            $return['wasInFavorites'] = true; //o 1
        }

        $return['fuck'] = "Mierda";

        return $return;
    }

    /**
     * Comprova si un llibre ja és a la taula books i si existeix, retorna el seu id
     * Si el bookId no existeix retorna 0. Si existeix... el seu id
     * @param type $bookId
     */
    public function checkBookInBooks($bookId) {
        $query = $this->dbConnection->prepare("SELECT id FROM books WHERE idApi = ?");
        $query->execute(array($bookId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $return = $data['id'];
        }
        return $return;
    }

    /**
     * Comprova si un llibre ja és a la taula userbooks
     * També aprofitem per extreure l
     * @param type $bookId
     */
    public function checkBookInUserBooks($bookId) {
        $query = $this->dbConnection->prepare("SELECT id FROM userbooks WHERE bookId = ?");
        $query->execute(array($bookId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $return = $data['id'];
        }
        return $return;
    }

    /*
     * Create a random string - La farem servir per crear noms d'imatges
     * @author	XEWeb <>
     * @param $length the length of the string to create
     * @return $str the string
     */

    function randomString($length) {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    /**
     * Retorna tots els llibres d'un usuari
     * @param type $userId
     * @return type
     */
    function getUserFavorites($userId) {
        $query = $this->dbConnection->prepare("SELECT b.id, b.idApi, b.title, b.authors, b.image, b.textSnippet FROM books b LEFT JOIN userbooks ub ON ub.bookId = b.id WHERE ub.userId = ?");
        $query->execute(array($userId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $return = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $return;
    }

    /**
     * Retorna tots els llibres d'un usuari paginats
     * @param type $userId
     * @param type $resultsPerPage
     * @param type $page
     * @return type
     */
    function getUserFavoritesPage($userId, $resultsPerPage, $page) {
        $start = $page * $resultsPerPage;

        //Lo hacemos un poco diferente ja que para usar LIMIT hacen falta parámetros INTEGER
        //https://stackoverflow.com/questions/10014147/limit-keyword-on-mysql-with-prepared-statement
        $sql = sprintf('SELECT b.id, b.idApi, b.title, b.authors, b.image, b.textSnippet FROM books b LEFT JOIN userbooks ub ON ub.bookId = b.id WHERE ub.userId = ? LIMIT %d, %d', $start, $resultsPerPage);
        $query = $this->dbConnection->prepare($sql);

        $query->execute(array($userId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $return = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $return;
    }

    /**
     * Retorna un llibre de guardat a la BD
     * @param type $bookId
     */
    function getFavorite($bookId) {
        $query = $this->dbConnection->prepare("SELECT id, idAPI, title, authors, image, textSnippet, description FROM books WHERE id = ?");
        $query->execute(array($bookId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $return = $query->fetch(PDO::FETCH_ASSOC);
        }
        return $return;
    }

    /**
     * Retorna el número total de favorits d'un usuari
     * @param type $userId
     */
    function getNumberOfFavorites($userId) {
        $query = $this->dbConnection->prepare("SELECT COUNT(id) FROM userbooks WHERE userId = ?");
        $query->execute(array($userId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $return = $data[0]['COUNT(id)'];
        }
        return $return;
    }

    /**
     * Elimina llibre de la llista de favorits d'un usuari
     * @param type $bookId
     */
    function deleteFavorite($bookId) {
        $query = $this->dbConnection->prepare("DELETE FROM userbooks WHERE bookId = ?");
        $query->execute(array($bookId));
    }

    /**
     * Saca el libro mejor valorado para la home
     */
    function getBestRatedBook() {
        $query = $this->dbConnection->prepare("SELECT r.userId, r.bookId, r.title, AVG(r.rating) avgrating, b.title, b.idApi, b.authors, b.image, b.description FROM reviews r LEFT JOIN books b ON r.bookId = b.id GROUP BY r.bookId ORDER BY AVG(r.rating) DESC LIMIT 1");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * Retorna tots els llibres a la base de dades pròpia en el format corresponent
     * @param type $action
     * @return type
     */
    function getAllBooksInBD($action) {
        switch ($action) {
            case 'az':
                $query = $this->dbConnection->prepare('SELECT * FROM books ORDER BY title ASC');
                break;
            case 'za':
                $query = $this->dbConnection->prepare('SELECT * FROM books ORDER BY title DESC');
                break;
            case 'best':
                $query = $this->dbConnection->prepare('SELECT SUM(r.rating), b.title, b.authors, b.image FROM books b LEFT JOIN reviews r ON b.id = r.bookId GROUP BY b.title ORDER BY rating DESC');
                break;
            case 'worst':
                $query = $this->dbConnection->prepare('SELECT SUM(r.rating), b.title, b.authors, b.image FROM books b LEFT JOIN reviews r ON b.id = r.bookId GROUP BY b.title ORDER BY rating ASC');
            case 'more':
                $query = $this->dbConnection->prepare('SELECT COUNT(r.id), b.title, b.authors, b.image FROM books b LEFT JOIN reviews r ON b.id = r.bookId GROUP BY b.title ORDER BY COUNT(r.id) DESC');
                break;
            case 'less':
                $query = $this->dbConnection->prepare('SELECT COUNT(r.id), b.title, b.authors, b.image FROM books b LEFT JOIN reviews r ON b.id = r.bookId GROUP BY b.title ORDER BY COUNT(r.id) ASC');
            default:
                $query = $this->dbConnection->prepare('SELECT * FROM books WHERE title LIKE "' . $action . '%"');
                break;
        }

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
