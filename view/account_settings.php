<?php
include_once("model/Form.php");
Layout::connection_restricted_area();
$layout->main_title("Espace privé");
echo("
  <div class='col-sm-12 row'>
    <div class='col-sm-2'></div>
    <div class='col-sm-3 text-center dark_bg'><a href='index.php?p=modif_user_info'><br/><p>Modifier mes informations personnelles</p></a></div>
    <div class='col-sm-2'></div>
    <div class='col-sm-3 text-center dark_bg'><a href='index.php?p=modif_user_profile'><br/><p>Compléter mon profil</p></a></div>
  </div>
  <div class='col-sm-12'>
  </div>
");

?>
