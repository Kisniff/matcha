<?php
//Session start
session_start();
include_once("../model/Notifs.php");
include_once("../model/Bdd.php");
Notifs::count_new_notifs();
?>
