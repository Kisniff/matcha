<?php
  session_start();
  if (!isset($_POST['name']) || !isset($_POST['action']))
    return ;
  $name = htmlspecialchars($_POST['name']);
  if ($_POST['action'] == "open")
    $_SESSION[$name] = "open";
  else
    $_SESSION[$name] = "close";
?>
