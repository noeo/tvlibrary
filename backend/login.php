<?php
require (dirname(__FILE__) . "/incl/init.php");

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (empty($_POST['username']) || empty($_POST['password'])) {
        header('Location: ' . URL);
        exit();
    }
    
    require (dirname(__FILE__) . "/incl/session.php");
    
    $login_username = $_POST['username'];
    $login_password = $_POST['password'];
    
    $login = loadf("autologin", $_POST['username'], $_POST['password']);
    
    if ($login == 1) {
        
        header('Location: ' . URL . 'index.php');
        exit();
    } else {
        
        if ($login == - 2) {
            $msg = "Dieses Benutzerkonto ist nicht für dieses Profil konfiguriert. Bitte überprüfen Sie die Berechtigung für die Region.";
        } else {
            
            $msg = "Bitte prüfen Sie Benutzername und Passwort";
        }
    }
}
require (dirname(__FILE__) . "/incl/_header.php");
?>
<div class="container">
	<div class="row">
		<div class="col-sm-6 col-md-4 col-md-offset-4">
			<h1 class="text-center login-title"><?=calltext("LOGIN_TITLE")?></h1>
			<p><?=calltext("LOGIN_TEXT")?></p>
			<div class="account-wall">
				<img class="profile-img" src="<?=calltext("LOGIN_IMAGE")?>" alt="">
				<form class="form-signin" action="<?=URL?>login.php" method="post">
					<input type="text" class="form-control" name="username"
						placeholder="Email" required autofocus> <input type="password"
						class="form-control" name="password" placeholder="Password"
						required>
					<button class="btn btn-lg btn-primary btn-block" type="submit">
						Einloggen</button>
					
				</form>
			</div>
		</div>
	</div>
</div>



<?php require (dirname(__FILE__) . "/incl/_footer.php");?>