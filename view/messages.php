<?php
Session_status::redirect_offline_user("Vous devez être connecté pour pouvoir effectuer une recherche avancee");
include_once("model/Messages.php");
include_once("model/Form.php");
$boxes_height = "45vh";

/*
** Box containing navigation and messages
*/
echo("<div class='col-sm-12'>");
echo("<div class='col-sm-12 row'>");

/*
** Get appropriate user id for conversation
*/
if (isset($_GET['id']))
  $_SESSION['id_msg'] = htmlspecialchars(intval($_GET['id']));
else
  $_SESSION['id_msg'] = 0;
if  (!(Messages::has_matched($_SESSION['id_msg'])))
  $_SESSION['id_msg'] = Messages::get_default_user();

/*
** Redirect user in case no matches are found
*/
if ($_SESSION['id_msg'] == -1)
{
  echo("<div class='col-sm-12 error_message text-center'>Vous n'avez pas encore de match !</div>");
  $layout->white_space(1);
  echo("<div class='col-sm-12 sub-title text-center'><a href='index.php?p=members'>Rendez-vous dans la section Membres pour commencer a liker des profils</a>");
  exit ;
}


/*
** Navigation section
*/
echo('<div class="col-sm-4 row">');
echo('<div class="col-sm-1"></div>');
echo('<div class="col-sm-9 dark_bg scroller" style="height:' . $boxes_height . '; overflow-y:scroll">');
$layout->white_space(1);
Messages::display_matched_users();
echo('</div>');
echo('<div class="col-sm-2"></div>');
echo('</div>');


/*
** Messages section
*/
echo('<div class="col-sm-8">');
echo('<div class="col-sm-12 dark_bg scroller" id="messages" style="height:' . $boxes_height . '; overflow-y:scroll">');
echo('<div class="col-sm-12">');
/*
** our messages will be displayed here
*/
echo('</div>');
echo('</div>');
echo('<div class="col-sm-12">');
Messages::write_messages();
echo('</div>');
echo('</div>');

/*
** Send message script
*/
if(isset($_POST['message']))
{
  Messages::send_message(htmlspecialchars($_POST['message']));
}

/*
** Mark appropriate message notification as red
*/
Messages::erase_new_msg_notif();

/*
** closing final div
*/
echo("</div>");
?>

 <script>

 var messages = $('#messages');

 /*
 ** scrollDown
 ** fonction qui permet de foutre le scroller tout en bas
 */
 function scrollDown()
 {
   messages.scrollTop(messages[0].scrollHeight);
 }

 /*
 ** updateMessage
 ** Appelée une fois la requete ajax terminée,
 ** traite le retour serveur
 */
 function updateMessage(code_html, statut)
 {
    messages[0].innerHTML = code_html;
    scrollDown();
 }

 /*
 ** GetMessage
 ** envoie la requete ajax au serveur
 ** met a jour la fenetre quand la requete est terminée
 */
 function getMessages()
 {
   var request = $.ajax({
     url : 'ajax_script/get_messages.php',
     type : 'POST',
     dataType : 'text'
   });
   request.done(updateMessage);
 }

 /*
 ** Appel Ajax, une fois au chargement de la page,
 ** puis toutes les 5 secondes
 */
 getMessages();
 // setInterval(getMessages, 5000);
 </script>
