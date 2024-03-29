<!DOCTYPE html>
<html>
<head>
  <title>Matcha</title>
  <meta charser="utf-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="view/main_styles.css">
  <link rel="shortcut icon" type="image/x-icon" href="view/matcha_logo_favicon.png" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body class="body">
<?php
  $layout->nav_bar();

  if (Session_status::is_connected() == false)
    return ;
?>

<script>
var notif = $("#notif")[0];
var msg = $("#msg")[0];

function updateNotifs(text, statut)
{
  notif.innerHTML = text;
}

function getNotifs()
{
 var request = $.ajax({
   url:'ajax_script/get_notifs.php',
   type:'POST',
   dataType:'jsonp'
 });
 request.done(updateNotifs);
}

function updateMessages(text, statut)
{
  msg.innerHTML = text;
}

function getMessages()
{
 var request = $.ajax({
   url:'ajax_script/get_msg_notif.php',
   type:'POST',
   dataType:'text'
 });
 request.done(updateMessages);
}

getNotifs();
getMessages();
setInterval(getNotifs, 5000);
</script>
