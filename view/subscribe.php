<?php
include_once("model/Form.php");
include_once("model/Bdd.php");
?>

<?php
  $layout->main_title("Rejoindre la communauté");
?>

<!-- Form -->
<?php
  $form = new Form("post", "index.php?p=subscribe");
  if (isset($_POST['login']))
    $form->entry("Login", "text", "login", $_POST['login']);
  else
    $form->entry("Login", "text", "login");
  if (isset($_POST['last_name']))
    $form->entry("Nom", "text", "last_name", $_POST['last_name']);
  else
    $form->entry("Nom", "text", "last_name");
  if (isset($_POST['first_name']))
    $form->entry("Prenom", "text", "first_name", $_POST['first_name']);
  else
    $form->entry("Prenom", "text", "first_name");
  if (isset($_POST['email']))
    $form->entry("Email", "email", "email", $_POST['email']);
  else
    $form->entry("Email", "email", "email");
  $form->entry("Mot de passe", "password", "pwd");
  $form->button("Envoyer");
  if ($form->are_all_subscription_fields_set())
  {
    $form->subscr_varpost_to_varsession();
    if ($form->subscription_validity())
      if ($pdo->add_user($_SESSION["email"], $_SESSION["login"], $_SESSION["first_name"], $_SESSION["last_name"], $_SESSION["pwd"]))
        $form->validation_message("Un email de confirmation vient detre envoyé a l'adresse " . $_SESSION["email"] . "");
      exit ;
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
?>
