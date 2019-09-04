<?php
include_once("model/Form.php");
include_once("config/auth_config.php");
?>

<?php
  $layout->main_title("Login");
?>

<!-- Form -->
<?php
  if (isset($_GET['key']) && isset($_GET['p']) && $_GET['p'] == 'Connexion')
  {
    $user_id = Bdd::get_user_field($_SESSION['email'], "id");
    Bdd::alter_table($user_id, "confirmed", $_GET['key']);
  }
  $form = new Form("post", "index.php?p=Connexion");
  if (isset($_POST['email']))
    $form->entry("Email", "email", "email", htmlspecialchars($_POST['email']));
  else
    $form->entry("Email", "email", "email");
  $layout->white_space(1);
  $form->entry("Mot de passe", "password", "pwd");
  $layout->white_space(1);
  $form->button("Envoyer");
  $layout->white_space(1);
  if ($_SESSION['connexion_status'] != "offline" && Form::are_connexion_fields_ok())
  {
    if (($ret = $pdo->are_logins_incorrect($_SESSION["email"], $_POST["pwd"])))
    {
      if ($ret == "mail")
        Form::field_error("Email", "Cet email ne correspond a aucun compte utilisateur");
      if ($ret == "password")
        Form::field_error("Mot de passe", "Mot de passe incorrect");
      if ($ret == "unconfirm")
        Form::field_error("Email", "Votre adresse mail n'a pas été confirmée, veuillez vérifier votre boîte de réception.");
    }
    else
      echo("<script>
      window.location.replace('index.php?p=connexion_landing_page');
      </script>");
  }
  $_SESSION['connexion_status'] = "attempt";
?>

<div class="col-sm-12 text-center">
  <a href="index.php?p=subscribe">Pas encore de compte ? Rejoins-nous !</a>
  <?=$layout->white_space(1);?>
  <a href="https://accounts.google.com/o/oauth2/v2/auth?scope=email&access_type=online&redirect_uri=<?= urlencode('http://localhost/matcha/index.php?p=connect') ?>&response_type=code&client_id=<?= $GOOGLE_ID?>">
    Se connecter avec Google !</a>
</div>
