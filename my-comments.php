<?php
require 'header.php';
require 'class/review.php';
$review = new Review($dbConnection);
?>

<div class="container">
    <div class="row top40 bottom40">
        <div class="col-sm-3">
            <?php require 'sidebar.php' ?>
        </div>

        <div class="col-sm-9">

            <?php
            $numberOfReviews = $review->getNumberOfReviews($_SESSION['id']);
            $resultsPerPage = 6;
            if (!isset($_GET['page'])) {
                $page = 0;
            } else {
                $page = $_GET['page'];
            }

            $reviews = $review->getUserReviewsPage($_SESSION['id'], $resultsPerPage, $page);
            ?>

            <div class="row">
                <h3>Has publicado <?php echo $numberOfReviews ?> crítica(s)</h3>
            </div>
            <?php //var_dump($reviews); ?>

            <?php foreach ($reviews as $r) { ?>
                <div class="row reviewRow">
                    <div class="col-sm-3">
                        <p><img src="<?php echo $r['image']; ?>"></p>
                    </div>

                    <div class="col-sm-9">

                        <?php for ($i = 0; $i < round($r['rating']); $i++) { ?>
                            <span class="fa fa-star checked"></span>
                        <?php } ?>  

                        <?php for ($i = round($r['rating']); $i < 5; $i++) { ?>
                            <span class="fa fa-star"></span>
                        <?php } ?>

                        <h5><?php echo $r['title']; ?> - <?php echo "<small>" . $r['authors'] . "</small>" ?></h5>
                        <p><?php echo $r['comment']; ?></p>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>

    <div class="paginator">
        
        <?php
        $numPagines = $numberOfReviews / $resultsPerPage;
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = strtok($url, '?');

        if ($numPagines > 1) {
            for ($i = 0; $i < $numPagines; $i++) {
                echo '<a href="' . $url . '?page=' . $i . '"><span class="paginatorNumber">' . ($i + 1) . '</span></a>';
            }
        }
        ?>

    </div>

    <?php require 'footer.php' ?>
