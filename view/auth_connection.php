<?php
include_once("config/auth_config.php");
include_once("model/Curl.php");

/*
** Get google json infos to call right api's url
** $main_json -> google json infos
*/
$Curl = new Curl($GOOGLE_INFO_FILE, "GET");
$main_json = json_decode($Curl->exec());
unset($Curl);
if ((isset($_SESSION["authorization_code"]) && isset($_GET['code'])
&& !strcmp($_SESSION["authorization_code"], htmlspecialchars($_GET['code']))) || (isset($_GET['code']) == false))
{
  $layout->main_error("Certificat Auth0 invalide");
}

/*
** $tokenEndPoint -> url to call to get access token
** posfields mandatory to get token from google api
*/
$tokenEndPoint = $main_json->token_endpoint;
$Curl = new Curl($tokenEndPoint, "POST");
$postfields = array(
  'code' => htmlspecialchars($_GET['code']),
  'client_id' => $GOOGLE_ID,
  'client_secret' => $GOOGLE_PWD,
  'redirect_uri' => $OUR_REDIRECTION_URL,
  'grant_type' => "authorization_code");
  $accessToken = json_decode($Curl->exec($postfields))->access_token;
  unset($Curl);

/*
** $userinfoEndPoint -> url to call to get user info
*/
$userinfoEndPoint = $main_json->userinfo_endpoint;
$Curl = new Curl($userinfoEndPoint, "GET");
$Curl->set_header(array("Authorization: Bearer " . $accessToken));
$userMail = json_decode($Curl->exec());
unset($Curl);
$_SESSION["authorization_code"] = htmlspecialchars($_GET['code']);
$_SESSION["email"] = $userMail->email;
include_once("connexion_landing_page.php");
?>
