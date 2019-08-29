<?php
session_start();
include_once("../model/Messages.php");
include_once("../model/Bdd.php");
$matched_users_id = Messages::get_matched_users();

function find_content_id()
{
  if (isset($_SESSION['#content_3']) && $_SESSION["#content_3"] == "open")
  {
    if (isset($_SESSION['#content_2']) && $_SESSION["#content_2"] == "open")
    {
      if (isset($_SESSION['#content_1']) && $_SESSION["#content_1"] == "open")
        return ("content_3");
      else
        return ("content_1");
    }
    else
      return ("content_2");
  }
  else
    return ("content_3");
}

echo("<div class='col-sm-12 '>");
foreach ($matched_users_id as $match)
{
  $matched_user_login = Bdd::get_field_with_conditions("users", "login", "id = '" . $match . "'")[0]['login'];
  $matched_user_picture = Messages::get_user_pic(unserialize(Bdd::get_field_with_conditions("users_profile", "images", "id = '" . $match . "'")[0]['images']));
  echo("
  <a class='no-deco col-sm-12 center' href='index.php?p=" . $_SESSION['page'] . "&id=" . $match . "&content=" . find_content_id() . "'>
  <div class='col-sm-12 row center'>");
  echo("<img class='col-sm-12 center mini_pic' src='" . $matched_user_picture . "'></div>
  <p class='italic col-sm-12 center'>" . $matched_user_login . "</p>
  </a>");
}
?>
