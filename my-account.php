<?php require 'header.php' ?>

<div class="container">
    <div class="row top40 bottom40">
        <div class="col-sm-3">
            <?php require 'sidebar.php' ?>
        </div>

        <div class="col-sm-9 card">
            <div class="card-body text-center">

                <?php $img = $user->getUserPicture($_SESSION['email']) ?>

                <?php if ($img['imgfound']) { ?>
                    <div><img src="<?php echo $_SESSION['image']; ?>"></div>
                <?php } else { ?>
                    <div><img src="img/default-user.png" alt="profile picture" class="rounded-circle" /></div>
                <?php } ?>
                <h3><?php echo $_SESSION['nickname']; ?></h3>
                <p class="blockquote"><?php echo $_SESSION['bio']; ?></div>
            </div>    
        </div>

    </div>
</div>

<?php require 'footer.php' ?>
