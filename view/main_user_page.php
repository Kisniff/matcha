<?php
include_once("model/Members.php");
echo('<div class="col-sm-12 row">
  <div class="col-sm-5">');
    $layout->main_title("Utilisateurs ayant visité votre profil");
    Members::display_visitors();
echo('</div>');



echo('<div class="col-sm-2"></div>');


echo('<div class="col-sm-5">');
    $layout->main_title("Utilisateurs ayant liké votre profil");
    Members::display_likers();


echo('</div>');




echo('</div>');
?>
