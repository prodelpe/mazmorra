<?php

class Review {

    private $dbConnection;

    function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Calcula nota mitjana d'un llibre
     * @param type $bookId
     * @return type
     */
    function averageRating($bookId) {
        $query = $this->dbConnection->prepare("SELECT AVG(rating) FROM reviews WHERE bookId = ?");
        $query->execute(array($bookId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $return = number_format($query->fetchAll(PDO::FETCH_ASSOC)[0]['AVG(rating)'], 2, ',', '');
        }
        return $return;
    }

    /**
     * Introdueix una crítica a la base de dades
     * @param type $userId
     * @param type $bookId
     * @param type $title
     * @param type $comment
     * @param type $rating
     * @return int
     */
    function setReview($userId, $bookId, $title, $comment, $rating) {
        $insertReview = $this->dbConnection->prepare("INSERT INTO reviews (userId, bookId, title, comment, rating) VALUES (?, ?, ?, ?, ?)");
        $insertReview->execute(array($userId, $bookId, $title, $comment, $rating));

        if ($insertReview->errorCode() == 0) {
            return $this->dbConnection->lastInsertId();
        } else {
            return 0;
        }
    }

    /**
     * Retorna el número total de comentaris d'un usuari
     * @param type $userId
     */
    function getNumberOfReviews($userId) {
        $query = $this->dbConnection->prepare("SELECT COUNT(id) FROM reviews WHERE userId = ?");
        $query->execute(array($userId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            $return = $data[0]['COUNT(id)'];
        }
        return $return;
    }
    
        /**
     * Retorna totes les reviews d'un usuari paginades
     * @param type $userId
     * @param type $resultsPerPage
     * @param type $page
     * @return type
     */
    function getUserReviewsPage($userId, $resultsPerPage, $page) {
        $start = $page * $resultsPerPage;
        
        //Lo hacemos un poco diferente ja que para usar LIMIT hacen falta parámetros INTEGER
        //https://stackoverflow.com/questions/10014147/limit-keyword-on-mysql-with-prepared-statement
        $sql = sprintf('SELECT * FROM reviews r LEFT JOIN books b ON r.bookId = b.id WHERE r.userId = ? LIMIT %d, %d', $start, $resultsPerPage);        
        $query = $this->dbConnection->prepare($sql);

        $query->execute(array($userId));
        $return = 0;
        if ($query->rowCount() > 0) {
            $return = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return $return;
    }
    
    /**
     * Muestra la última crítica escrita
     * @return type
     */
    function getLastReview(){
        $query = $this->dbConnection->prepare("SELECT * FROM reviews r LEFT JOIN books b ON r.bookId = b.id ORDER BY r.id DESC LIMIT 1");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC)[0];
    }

}
