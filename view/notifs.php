<?php
Session_status::redirect_offline_user("Vous devez être connecté pour pouvoir effectuer une recherche avancee");
include_once("model/Notifs.php");

$layout->main_title("Notifications");
Notifs::display_notifs();
?>
