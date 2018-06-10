<?php

/* Conexión a la BD con PDO... es así de fácil */

// Localhost
$dbConnection = @new PDO("mysql:dbname=mazmorra;host=localhost", "root", "");
?>