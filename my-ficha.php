<?php
require 'header.php';
require 'class/book.php';
$book = new Book($dbConnection);
$bookData = $book->SingleBookInAPI($_GET['idAPI']);
?>

<div class="container">
    <div class="row top40 bottom40">
        <div class="col-sm-3">
            <?php require 'sidebar.php' ?>
        </div>

        <div class="col-sm-9 rightColumn card">

            <div class="row">

                <div class="col-sm-3">
                    <img src="<?php echo $bookData['image']; ?>">
                </div>

                <div class="col-sm-9">

                    <?php if (isset($bookData['error']) && $bookData['error'] == 1) { ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Error!</strong> <?php echo $bookData['message']; ?>
                        </div>
                    <?php } else { ?>

                        <h1><?php echo $bookData['title']; ?></h1>
                        <h3><?php echo $bookData['authors']; ?></h3>
                        <p>Publicado por: <?php echo $bookData['publisher']; ?></p>
                        <p><?php echo $bookData['pageCount']; ?> páginas</p>
                    <?php } ?>
                </div><!-- End Col-sm-8 -->
            </div><!-- End Row 1-->

            <div class="row">
                <div class="col-sm-12 description">
                    <?php echo $bookData['description']; ?>
                    <p><?php echo $bookData['categories']; ?></p>
                </div>
            </div><!-- End Row 2-->

            <div class="row">
                <div id="boton" class="col-sm-12">
                    <button type="button" class="btn btn-primary" onclick="addBiblio('<?php echo $_SESSION['id']?>', '<?php echo $bookData['id']; ?>')">Añadir a mi biblioteca</button>
                </div>
            </div><!-- End Row 3-->
        </div>
    </div>
</div>

<?php require 'footer.php' ?>
