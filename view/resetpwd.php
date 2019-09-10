<?php
include_once("model/Form.php");
include_once("config/auth_config.php");
?>

<?php
  $layout->main_title("Réinitialisation de votre mot de passe");
?>

<!-- Form -->
<?php
  if (isset($_GET['key']) && isset($_GET['p']) && $_GET['p'] == 'resetpwd') {
    $key = $_GET['key'];
    $keystr = "&key=".$key;
  }
  else {
    $key = 1;
    $keystr = "";
  }
  $form = new Form("post", "index.php?p=resetpwd".$keystr);
  if (isset($_POST['email']))
    $form->entry("Email", "email", "email", htmlspecialchars($_POST['email']));
  else
    $form->entry("Email", "email", "email");
  $layout->white_space(1);
  $form->entry("Nouveau mot de passe", "password", "pwd");
  $layout->white_space(1);
  $form->button("Envoyer");
  $layout->white_space(1);
  if ($_SESSION['connexion_status'] == "offline" && Form::are_connexion_fields_ok())
  {
    if (($ret = $pdo->reset_pwd($_SESSION["email"], $_POST["pwd"], $key)))
    {
      if ($ret == "mail")
        Form::field_error("Email", "Cet email ne correspond a aucun compte utilisateur");
      if ($ret == "unconfirm")
        Form::field_error("Email", "Votre adresse mail n'a pas été confirmée, veuillez vérifier votre boîte de réception.");
      if ($ret =="invalid key")
        Form::field_error("Email", "Vous essayez de réinitialiser le mot de passe d'un compte qui ne vous appartient pas...");
    }
    else 
      Form::validation_message("Votre mot de passe a bien été modifié.");
  }
  $_SESSION['connexion_status'] = "attempt";
?>

<div class="col-sm-12 text-center">
  <a href="index.php?p=subscribe">Pas encore de compte ? Rejoins-nous !</a>
  <?=$layout->white_space(1);?>
  <a href="https://accounts.google.com/o/oauth2/v2/auth?scope=email&access_type=online&redirect_uri=<?= urlencode('http://localhost/matcha/index.php?p=connect') ?>&response_type=code&client_id=<?= $GOOGLE_ID?>">
    Se connecter avec Google !</a>
</div>
