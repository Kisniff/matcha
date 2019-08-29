<?php
include_once("model/Form.php");
Layout::connection_restricted_area();

$layout->main_title("Gestion des informations personnelles");
$form = new Form("post", "index.php?p=modif_user_info");
$form->entry("Nom", "text", "last_name");
$form->entry("Prenom", "text", "first_name");
$form->entry("Email", "text", "email");
$form->entry("Mot de passe", "password", "pwd");
$form->button("Modifier mes infos !");
Form::modif_user_vars();
?>
