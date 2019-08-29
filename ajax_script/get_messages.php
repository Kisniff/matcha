<?php
//Session start
session_start();
include_once("../model/Messages.php");
include_once("../model/Bdd.php");
Messages::display_messages();

?>
