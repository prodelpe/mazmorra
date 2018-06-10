<?php
require 'header.php';
?>

<main role="main">

    <div class="top80">

        <div class="container">

            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger">
                    <strong>Error!</strong> <?php echo $_GET['message']; ?>
                </div>
            <?php } ?>

            <div class="row">

                <div class="col-sm-4 col-xs-12 offset-sm-1">
                    <h2>Regístrate</h2>
                    <a class="btn btn-lg btn-primary btn-block" href="register.php">Crear una cuenta</a>
                </div>

                <div class="col-sm-4 col-xs-12 offset-sm-1">

                    <form class="form-signin" action="action/action-login.php" method="POST">
                        <h2 class="form-signin-heading">Mi cuenta</h2>
                        <label for="inputEmail" class="sr-only">Email address</label>
                        <input type="email" name="inputEmail" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" name="inputPassword" id="inputPassword" class="form-control" placeholder="Password" required>
                        <button class="btn btn-lg btn-primary btn-block" type="submit" name="enviar">Sign in</button>
                    </form>

                </div>

            </div>

        </div> <!-- /container -->

    </div><!-- Jumbotron -->
</main>


<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
<?php
require 'footer.php';
?>
