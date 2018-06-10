<?php
require 'header.php';
require 'class/book.php';
$book = new Book($dbConnection);
?>

<div class="container">
    <div class="row top40 bottom40">
        <div class="col-sm-3">
            <?php require 'sidebar.php' ?>
        </div>

        <div class="col-sm-9 rightColumn">
            <div id="searchForm">
                <form class="form-inline my-2 my-lg-0" action="my-biblio.php" method="GET">
                    <input name="search" class="form-control mr-sm-2" type="text" placeholder="Libro, autor, etc." aria-label="Search">
                    <input name="index" type="hidden" value="0">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
                </form>
            </div>

            <div id="searchResults">

                <?php
                if (isset($_GET['search'])) {
                    $APIresult = $book->searchInAPI($_GET['search'], $_GET['index']);

                    //Si no se encuentran resultados...
                    if (isset($APIresult['error']) && $APIresult['error'] == 1) {
                        ?><div class="alert alert-danger"><?php
                        echo $APIresult['message'];
                        ?></div><?php
                    } else {

                        for ($i = 0; $i < 3; $i++) {
                            ?>
                            <div class="row">
                                <?php foreach ($APIresult as $book) { ?>

                                    <div class="col-sm-4 col-xs-12 ficha">
                                        <a href="my-ficha.php?idAPI=<?php echo $book['idAPI']; ?>">
                                            <p class="imagen"><img src="<?php echo $book['image']; ?>"></p>
                                        </a>
                                        <h4><?php echo $book['title']; ?></h4>
                                        <p><b><?php echo $book['authors']; ?></b></p>
                                        <p><?php echo $book['textSnippet']; ?></p>
                                    </div>

                                    <?php
                                }//End cell
                                ?></div><?php
                            break; //Salimos del bucle completada una vuelta
                        }//End Row
                    }//End Else si se ha encontrado
                } else {//Si no s'ha posat el search...
                    $totalFavorites = $book->getNumberOfFavorites($_SESSION['id']);
                    //$userFavorites = $book->getUserFavorites($_SESSION['id']);
                    //Preparem paginació
                    $resultsPerPage = 6;
                    if (!isset($_GET['page'])) {
                        $page = 0;
                    } else {
                        $page = $_GET['page'];
                    }

                    $userFavorites = $book->getUserFavoritesPage($_SESSION['id'], $resultsPerPage, $page);
                    ?>
                    <div class="row">
                        <h3>Tienes <?php echo $totalFavorites ?> favorito(s) en tu Biblioteca</h3>
                    </div>
                    <div class="row">
                        <?php foreach ($userFavorites as $favorite) { ?>
                            <?php //var_dump($favorite);  ?>

                            <div class="col-sm-4 col-xs-12 ficha">
                                <a href="my-ficha.php?idAPI=<?php echo $favorite['idAPI']; ?>">
                                    <p class="imagen">
                                        <img src="<?php echo $favorite['image']; ?>">
                                    </p>
                                </a>
                                <h4><?php echo $favorite['title']; ?></h4>
                                <p><b><?php echo $favorite['authors']; ?></b></p>
                                <p><?php echo $favorite['textSnippet']; //TODO      ?></p>
                                <a href="my-review.php?id=<?php echo $favorite['id']; ?>" class="btn btn-primary botones">Añadir crítica</a>
                                <a href="action/action-delete.php?id=<?php echo $favorite['id']; ?>" class="btn btn-danger botones">Quitar de favoritos</a>
                            </div>

                            <?php
                        }//End cell
                        ?></div><?php }
                    ?>
                <div class="paginator">
                    <?php
                    $numPagines = $totalFavorites / $resultsPerPage;
                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $url = strtok($url, '?'); //Ens carreguem els paràmetres si existeixen

                    if ($numPagines > 1) {
                        for ($i = 0; $i < $numPagines; $i++) {
                            echo '<a href="' . $url . '?page=' . $i . '"><span class="paginatorNumber">' . ($i + 1) . '</span></a>';
                        }
                    }
                    ?>

                </div>
            </div>
            <?php if (!isset($APIresult['error']) && isset($_GET['index'])) { ?>
                <nav id="prev-next">
                    <?php if ($_GET['index'] != 0 && !isset($APIresult['error'])) { ?>
                        <a href="my-biblio.php?search=<?php echo $_GET['search']; ?>&index=<?php echo (int) $_GET['index'] - 30; ?>" class="previous">&laquo; Anterior</a>
                    <?php } ?>
                    <a href="http://localhost:81/mazmorra/my-biblio.php?search=<?php echo $_GET['search']; ?>&index=<?php echo (int) $_GET['index'] + 30; ?>" class="next">Siguiente &raquo;</a>
                </nav>
            <?php } ?>
        </div>
    </div>



    <?php require 'footer.php' ?>
