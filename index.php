<?php
require 'header.php';
require 'class/book.php';
require 'class/review.php';
$book = new Book($dbConnection);
$review = new Review($dbConnection);

//Sacamos el contenido de los tres headers de abajo
$bestRatedBook = $book->getBestRatedBook();
$lastReview = $review->getLastReview();
$mostActiveUser = $user->getMostActiveUser();
?>
<main role="main">

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
        <div class="container">
            <h1 class="display-3">Bienvenidos!!</h1>
            <p>Este proyecto ha sido realizado nada más que para practicar PHP. El usuario puede crearse una biblioteca y publicar sus críticas: conectamos a una API, guardamos sus datos en nuestra BD si el usuario lo solicita y habilitamos un CRUD.</p>
            <p><a class="btn btn-primary btn-lg" href="catalog.php" role="button">Consulta nuestro catálogo &raquo;</a></p>
        </div>
    </div>

    <div class="container" id="columnasHome">
        <!-- Example row of columns -->
        <div class="row best">
            <div class="col-md-4 card">
                <h2>Obra mejor valorada</h2>
                <h4><?php echo $bestRatedBook['title'] ?></h4>

                <p>
                    <?php for ($i = 0; $i < round($bestRatedBook['avgrating']); $i++) { ?>
                        <span class="fa fa-star checked"></span>
                    <?php } ?>  

                    <?php for ($i = round($bestRatedBook['avgrating']); $i < 5; $i++) { ?>
                        <span class="fa fa-star"></span>
                    <?php } ?>
                </p>

                <p><img src="<?php echo $bestRatedBook['image'] ?>"></p>
                <p><?php echo substr($bestRatedBook['description'], 0, 150) . '...' ?></p>

                <p><a class="btn btn-secondary" href="#" role="button">Ver Ficha &raquo;</a></p>
            </div>
            <div class="col-md-4 card">
                <h2>Última crítica añadida</h2>
                <h4><?php echo $lastReview['title'] ?></h4>

                <p>
                    <?php for ($i = 0; $i < round($lastReview['rating']); $i++) { ?>
                        <span class="fa fa-star checked"></span>
                    <?php } ?>  

                    <?php for ($i = round($lastReview['rating']); $i < 5; $i++) { ?>
                        <span class="fa fa-star"></span>
                    <?php } ?>
                </p>

                <p><img src="<?php echo $lastReview['image'] ?>"></p>
                <p><?php echo substr($lastReview['comment'], 0, 150) . '...' ?></p>

                <p><a class="btn btn-secondary" href="#" role="button">Ver crítica &raquo;</a></p>
            </div>
            <div class="col-md-4 card">
                <h2>Usuario más activo</h2>
                <h4><?php echo $mostActiveUser['nickname'] ?></h4>
                <p></p>
                <p><img src="<?php echo $mostActiveUser['image'] ?>" width=250px></p>               
                <p>Número de críticas: <?php echo $mostActiveUser['totalReviews'] ?></p>
                <p><a class="btn btn-secondary" href="#" role="button">View details &raquo;</a></p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 nota">
                <p><pre>NOTA del autor: Puntos que faltan por hacer: ficha de obra, de crítica y de usuario. Paginador en el catálogo. Buscador.</pre></p>
            </div>
        </div>
    </div> <!-- /container -->

</main>

<?php require 'footer.php' ?>