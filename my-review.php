<?php
require 'header.php';
require 'class/book.php';
require 'class/review.php';
$book = new Book($dbConnection);
$review = new Review($dbConnection);
$bookData = $book->getFavorite($_GET['id']);
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
                        <br>
                        <h1><small>Nota de nuestros usuarios: </small><?php echo $review->averageRating($bookData['id']); ?></h1>
                    <?php } ?>
                </div><!-- End Col-sm-8 -->
            </div><!-- End Row 1-->

            <div class="row">
                <div class="col-sm-12">
                    <form action="action/action-review.php" method="GET" style="padding-top: 20px">
                        
                        <input name="userId" id="userId" type="hidden" value="<?php echo $_SESSION['id']; ?>">
                        <input name="bookId" id="bookId" type="hidden" value="<?php echo $bookData['id']; ?>">

                        <div id="stars" class="form-group">
                            <span class="fa fa-star" data-val="1"></span>
                            <span class="fa fa-star" data-val="2"></span>
                            <span class="fa fa-star" data-val="3"></span>
                            <span class="fa fa-star" data-val="4"></span>
                            <span class="fa fa-star" data-val="5"></span>
                        </div>
                        
                        <input name="rating" id="rating" type="hidden" value="0">

                        <div class="form-group">
                            <input type="text" name="titulo" class="form-control" id="titulo" placeholder="Título" required>
                        </div>

                        <div class="form-group">
                            <textarea class="form-control" name="critica" id="critica" rows="3"  placeholder="Crítica" required></textarea>
                        </div>

                          <button type="submit" class="btn btn-primary">Añadir crítica</button>

                    </form>
                </div>
            </div><!-- End Row 2-->
        </div>
    </div>
</div>

<?php require 'footer.php' ?>
