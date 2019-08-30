<?php
// $dsn="mysql:host=localhost:8100;";
// $login = "root";
// $pwd="mthiery";
// if ($db = new PDO($dsn, $login, $pwd))
//   {
//     print("cool");
//   }
header("Access-Control-Allow-Origin: http://www.geoplugin.com/");
session_start();

//common model includes
include_once("model/Layout.php");
include_once("model/Bdd.php");
include_once("model/Session_status.php");
include_once("config/database.php");
$pdo = new Bdd($DB_DSN, $DB_USER, $DB_PASSWORD);
// $pdo->init_bdd();
try {
	$pdo->query("use matcha;");
	$pdo->query("SELECT * FROM users");
  $pdo->query("SELECT * FROM users_profile");
  // print("haha");
}
catch (Exception $e){
	$pdo->init_bdd();
	$_SESSION['connexion_status'] = "offline";
	echo("<script>windowd.location.change('index.php');</script>");
}

//common variables
$layout = new Layout();
Session_status::page_handler();
Session_status::connexion_status();
//header
include_once("view/header.php");
?>

<?php
//redirection
if ($_SESSION['page'] == "subscribe")
  include_once("view/subscribe.php");
else if ($_SESSION['page'] == "connexion_landing_page")
  include_once("view/connexion_landing_page.php");
else if ($_SESSION['page'] == "account_settings")
  include_once("view/account_settings.php");
else if ($_SESSION['page'] == "Connexion" || $_SESSION['page'] == "Deconnexion")
  include_once("view/login.php");
else if ($_SESSION['page'] == "connect")
  include_once("view/auth_connection.php");
else if ($_SESSION['page'] == "Deconnexion")
  include_once("view/login.php");
else if ($_SESSION['page'] == "members")
  include_once("view/members.php");
else if ($_SESSION['page'] == "member_profile")
  include_once("view/member_profile.php");
else if ($_SESSION['page'] == "modif_user_info")
  include_once("view/modif_user_info.php");
else if ($_SESSION['page'] == "notifs")
  include_once("view/notifs.php");
else if ($_SESSION['page'] == "messages")
  include_once("view/messages.php");
else if ($_SESSION['page'] == "extended_search")
  include_once("view/extended_search.php");
else if ($_SESSION['page'] == "modif_user_profile" || $_SESSION['page'] == "del" || $_SESSION['page'] == "upload" ||  $_SESSION['page'] == "del_pic")
  include_once("view/modif_user_profile.php");
else if ($_SESSION['connexion_status'] == "connected")
	include_once("view/main_user_page.php");
else if ($_SESSION['connexion_status'] == "offline")
	include_once("view/main_offline_page.php");
?>

<?php
// footer
$layout->white_space(4);
include_once("view/footer.php");
?>
