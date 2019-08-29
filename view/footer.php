<?php
  /*
  ** Acces restricted
  */
  if (isset($_SESSION['connexion_status']) && $_SESSION['connexion_status'] != "connected")
    exit ;
  include_once("model/Messages.php");
  include_once("model/Form.php");

  /*
  ** When user open new bubble message
  */
  // if (isset($_GET['content']) && isset($_GET['id']))
  // {
  //   $_SESSION["#" . htmlspecialchars($_GET['content'])] = "open";
  //   $_SESSION["match_" . htmlspecialchars($_GET['content'])] = htmlspecialchars($_GET['id']);
  //   echo("match_" . htmlspecialchars($_GET['content']));
  // }
  //
  // function display_msgs($name)
  // {
  //   if (isset($_SESSION["#" . $name]) && $_SESSION['#' . $name] == "open")
  //   {
  //     echo('<div class="col-sm-3 foot_msg_content" id="box_' .  $name . '">');
  //     echo('<div class="col-sm-12 foot_msg_content" id="' . $name . '" style="overflow-y:scroll">');
  //     if (isset($_SESSION["match_" . $name]))
  //     {
  //       Messages::display_messages($_SESSION["match_" . $name]);
  //       Messages::write_messages("mini");
  //     }
  //     echo("</div>");
  //     echo("</div>");
  //     echo("</div>");
  //   }
  //   else
  //     echo('<div class="col-sm-3 hidden foot_msg_content" id="' . $name . '">colonne content</div>');
  // }
  /*
  ** DEBUG PURPOSE
  */
  // print_r($_SESSION);
  /*
  **
  */
  // $layout->white_space(4);
  echo('<footer class="text-center">');
  // echo('<div class="max_width col-sm-12 row">');
  // for ($i = 1; $i <= 3; $i++)
  //   display_msgs("content_" . $i);
  // if (isset($_SESSION["msg_menu_content"]) && $_SESSION["msg_menu_content"]  == "open")
  //     echo('<div class="col-sm-3 foot_msg_content" id="msg_menu_content"></div>');
  // else
  //     echo('<div class="col-sm-3 hidden foot_msg_content" id="msg_menu_content"></div>');
  // echo('</div>');
  // echo('<div class="max_width col-sm-12 row">');
  // for ($i = 1; $i <= 3; $i++)
  //   display_title($i);
  // echo('<div id="msg_menu" class="col-sm-3 foot_msg_title">Messages instantannés</div>');
  // echo('</div>');
  echo('
  <div class="foot_msg_title">Ce footer ne sert a rien ©</div>
  </footer>
  ');

  function display_title($name)
  {
    if (isset($_SESSION["#content_" . $name]) && $_SESSION['#content_' . $name] == "open")
      echo('<div class="col-sm-3 foot_msg_title" id="title_' . $name . '">colonne title</div>');
    else
      echo('<div class="col-sm-3 hidden foot_msg_title" id=title_"' . $name . '">colonne title</div>');
  }
?>
</body>
</html>

<script>
/*
** Permet de savoir quelels bulles de conversation sont ouvertes
*/
function record_mini_msg_status(name, action)
{
 var request = $.ajax({
   url:'ajax_script/record_open.php',
   type:'POST',
   data:"name=" + name +"&action=" + action,
   dataType:'text'
 });
}

function add_remove_hidden(name, array)
{
  var new_array = Array();

  ret = -1;
  for (i = 0; i < array.length; i++)
    if (array[i] != "hidden")
      new_array.push(array[i]);
    else
      ret++;
  if (ret < 0)
  {
    new_array.push("hidden");
    record_mini_msg_status(name, "close");
  }
  else
    record_mini_msg_status(name, "open");
  return new_array;
}

function new_class(name, attr)
{
  attributes = attr.split(" ");
  attributes = add_remove_hidden(name, attributes);
  return (attributes.join(" "));
}

/*
** Handle every message class
*/
$("#title_1").click(function(){
  $("#content_1").attr("class", new_class("#content_1", $("#content_1").attr("class")));
  $("#box_content_1").attr("class", new_class("#box_content_1", $("#box_content_1").attr("class")));
});
$("#title_2").click(function(){
  $("#content_2").attr("class", new_class("#content_2", $("#content_2").attr("class")));
  $("#box_content_2").attr("class", new_class("#box_content_2", $("#box_content_2").attr("class")));
});
$("#title_3").click(function(){
  $("#content_3").attr("class", new_class("#content_3", $("#content_3").attr("class")));
  $("#box_content_3").attr("class", new_class("#box_content_3", $("#box_content_3").attr("class")));
});

/*
** Handle message main menu
*/
$("#msg_menu").click(function(){
    var attrs = $("#msg_menu_content").attr("class");
    attrs = new_class("#msg_menu_content", attrs);
    $("#msg_menu_content").attr("class", attrs);
});

function updateMessages(text, status)
{
  // alert(text);
}

function updateMatches(text, status)
{
  // alert("footer.php, go to updateMatches and do the work");
  $("#msg_menu_content").append(text);

}

/*
** ajax call
*/
function getMessages()
{
 var request = $.ajax({
   url:'ajax_script/get_foot_msg_title_content.php',
   type:'POST',
   dataType:'text'
 });
 request.done(updateMessages);
}

function getMatches()
{
 var request = $.ajax({
   url:'ajax_script/get_mini_matches.php',
   type:'POST',
   dataType:'text'
 });
 request.done(updateMatches);
}

function sendMessage()
{
 var request = $.ajax({
   url:'ajax_script/send_mini_msg.php',
   type:'POST',
   dataType:'text'
 });
 request.done(getMessages);
}

getMessages();
getMatches();
$("#send").click(sendMessage);
</script>
