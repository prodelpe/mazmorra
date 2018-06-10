<?php require 'header.php' ?>

<main role="main">

    <div class="container top80">
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger">
                <strong>Error!</strong> <?php echo $_GET['message']; ?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-sm-6 offset-sm-3">
                <h2 class="form-signin-heading">Crea una cuenta</h2>
                <div class="well">
                    <form autocomplete="off" action="action/action-register.php" method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Nombre de usuario" name="inputNickname" required="" autofocus="" tabindex="1">
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Email" name="inputEmail" required="" tabindex="2">
                        </div>
                        <div class="form-group">
                            <input type="Password" class="form-control" placeholder="Password" name="inputPassword" required="" tabindex="3">
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="conditions" value="conditions" required="" tabindex="4"> He leído y acepto las condiciones de uso y política de privacidad de la web
                            </label>
                        </div>
                        <input type="submit" name="enviar" value="Crear una cuenta" class="btn btn-lg btn-primary btn-block" tabindex="5">
                    </form>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <hr>

    </div> <!-- /container -->

</main>

<?php require 'footer.php' ?>