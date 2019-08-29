<?php
session_start();
include_once("../Model/Messages");
if (isset($_POST['msg']))
  Messages::send_message(htmlspecialchars($_POST['msg']));
?>
