<?php require 'header.php' ?>

<div class="container">
    <div class="row top40 bottom40">
        <div class="col-sm-3">
            <?php require 'sidebar.php' ?>
        </div>

        <div class="col-sm-9 card">
            <div class="card-body text-center">

                <?php if (isset($_GET['error'])) { ?>
                    <?php if ($_GET['error'] == 1) { ?>
                        <div class="alert alert-success" role="alert">
                            <strong>Hecho!</strong> <?php echo $_GET['message']; ?>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Error!</strong> <?php echo $_GET['message']; ?>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php 
                $img = $user->getUserPicture($_SESSION['email']);
                $userData = $user->getUserData($_SESSION['email']);
                ?>

                <form id="updateform" action="action/action-update.php" method="POST" enctype="multipart/form-data">

                    <div class="form-group offset-md-3 col-sm-6">

                        <?php if ($img['imgfound']) { ?>
                            <div><img src="<?php echo $_SESSION['image']; ?>"></div>
                        <?php } else { ?>
                            <div><img src="img/default-user.png" alt="profile pìcture" class="rounded-circle" /></div>
                        <?php } ?>

                        <div class="custom-file mbottom20">
                            <input type="file" id="imagen" name="imagen" lang="es">
                            <label class="custom-file-label" for="imagen">Seleccionar Imagen</label>
                        </div>

                        <div class="mbottom20">
                            <label>Nombre de usuario</label>
                            <input class="form-control" type="text" name="nickname" placeholder="<?php echo $userData['nickname']; ?>"/>
                        </div>

                        <div class="mbottom20">
                            <label>Biografia</label>
                            <textarea class="form-control" name="bio" placeholder="<?php echo $userData['bio']; ?>" /></textarea>
                        </div>
                        <input name="enviar" type="submit" />
                    </div>

                </form>

            </div>    
        </div>

    </div>
</div>

<?php require 'footer.php' ?>
