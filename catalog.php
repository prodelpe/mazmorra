<?php
require 'header.php';
require 'class/book.php';
require 'class/review.php';
$book = new Book($dbConnection);
?>

<div class="container">
    <nav class="navbar navbar-expand-md">
        <div class="col-sm-8">
            <ul class="navbar-nav mr-auto alfabeto">

                <?php
                foreach (range('A', 'Z') as $i) {
                    echo '<li class="nav-item"><a class="nav-link" href="catalog.php?action=' . $i . '">' . $i . '</a></li>';
                }
                ?>
            </ul>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <form action="catalog.php">
                    <select  class="form-control" id="orden">
                        <option value="az">Por nombre [A-Z]</option>
                        <option value="za">Por nombre [Z-A]</option>
                        <option value="best">Mejor valorados</option>
                        <option value="worst">Peor valorados</option>
                        <option value="more">Más seguidores</option>
                        <option value="less">Menos seguidores</option>
                    </select>
                </form>
            </div>
        </div>
    </nav>


    <main>
        <?php
        //Recollim ordre dels llibres
        if (!isset($_GET['action'])) {
            $action = 'az';
        } else {
            $action = $_GET['action'];
        }

        //Recollim tots els llibres de la BD en l'ordre corresponent
        $catalog = $book->getAllBooksInBD($action);
        //var_dump($catalog);
        ?>
        <div class="row">
            <?php foreach ($catalog as $c) { ?>

                <div class="col-md-3 ficha">
                    <h4><?php echo $c['title']; ?></h4>
                    <h6><?php echo $c['authors']; ?></h6>
                    <img src="<?php echo $c['image']; ?>">
                </div>
            <?php } ?>
        </div><!--End Row-->
    </main>

</div><!-- End Container -->

<?php require 'footer.php' ?>

