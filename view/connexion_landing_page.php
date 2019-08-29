<?php
include_once("model/Form.php");
/*
** Si l'utilisateur se conecte pour la premiere fois via googe -> formulaire
*/
if (!($pdo->user_exist($_SESSION['email'])))
{
  $layout->main_title("Bienvenue parmis nous !", "Nous avons encore besoin de quelques petites informations pour valider ton inscription !");
  $form = new Form("post", "index.php?p=connexion_landing_page");
  if (isset($_POST['login']))
    $form->entry("Login", "text", "login", htmlspecialchars($_POST['login']));
  else
    $form->entry("Login", "text", "login");
  if (isset($_POST['last_name']))
    $form->entry("Nom", "text", "last_name", htmlspecialchars($_POST['last_name']));
  else
    $form->entry("Nom", "text", "last_name");
  if (isset($_POST['first_name']))
    $form->entry("Prenom", "text", "first_name", htmlspecialchars($_POST['first_name']));
  else
    $form->entry("Prenom", "text", "first_name");
  $form->entry("Mot de passe", "password", "pwd");
  $form->button("Envoyer");
  if ($form->are_all_google_subscription_fields_set())
  {
    $form->subscr_varpost_to_varsession();
    if ($form->subscription_validity())
      if ($pdo->add_user($_SESSION["email"], $_SESSION["login"], $_SESSION["first_name"], $_SESSION["last_name"], $_SESSION["pwd"]))
        $form->validation_message("Un email de confirmation vient detre envoyé a l'adresse " . $_SESSION["email"] . "");
  }
  else if ($_SESSION['connexion_status'] == 'attempt')
  {
    echo("
    <script>
    alert('Veuillez renseigner tous les champs');
    </script>");
  }
  if ($_SESSION['connexion_status'] == "offline")
    $_SESSION['connexion_status'] = "attempt";
}
else
{
  $_SESSION["connexion_status"] = "connected";
  $_SESSION['id'] = Bdd::get_user_field($_SESSION["email"], "id");
  echo("
  <script>
  window.location.replace('index.php')
  </script>
  ");
}
?>
