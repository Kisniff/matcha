<?php
session_start();
include_once("../model/Messages.php");
include_once("../model/Bdd.php");
Messages::count_new_msg_notif();
?>
