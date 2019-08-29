<?php
include_once("model/Member_profile.php");
Session_status::redirect_offline_user("Vous devez être connecté pour voir le profil des autres utilisateurs");
if (isset($_GET["id"]))
  $_SESSION["member_id"] = (htmlspecialchars($_GET["id"]));
Member_profile::does_member_exist();
if (isset($_GET["action"]) && htmlspecialchars($_GET["action"]) == "like" && $_SESSION['member_id'] != $_SESSION['id'])
  Member_profile::like_user();
if (isset($_GET["action"]) && htmlspecialchars($_GET["action"]) == "block" && $_SESSION['member_id'] != $_SESSION['id'])
  Member_profile::block_user();
if (isset($_GET["action"]) && htmlspecialchars($_GET["action"]) == "report" && $_SESSION['member_id'] != $_SESSION['id'])
  Member_profile::report_user();
Member_profile::display_user();
if ( $_SESSION['member_id'] != $_SESSION['id'])
  Member_profile::send_notif();
?>

<script>
var screenWidth = window.innerWidth;
var imgs = document.getElementsByClassName('photo');
for(var i = 0; i < imgs.length; i++)
{
  imgs[i].style.height = screenWidth / 4 +'px';
  imgs[i].style.width = 'auto';
}
</script>
