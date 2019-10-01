<?php
class Messages
{
  public static function get_matched_users()
  {
      return (self::to_array(unserialize(base64_decode(Bdd::get_field_with_conditions("users", "matches", "id = '" . $_SESSION['id'] . "'")[0]['matches']))));
  }

  public static function get_messages($id)
  {
      $messages = Bdd::get_field_with_conditions("matched_conv", "*", "id_member_a = '" . $_SESSION['id'] . "' AND id_member_b = '" . $_SESSION['id_msg'] . "'");
      if (isset($messages[0]))
        return ($messages[0]['messages']);
      $messages = Bdd::get_field_with_conditions("matched_conv", "*", "id_member_a = '" . $_SESSION['id_msg'] . "' AND id_member_b = '" . $_SESSION['id']  . "'");
      if (isset($messages[0]))
        return ($messages[0]['messages']);
  }

  private static function get_conv_id()
  {
    $id = Bdd::get_field_with_conditions("matched_conv", "id", "id_member_a = '" . $_SESSION['id'] . "' AND id_member_b = '" . $_SESSION['id_msg'] . "'");
    if (!isset($id[0]))
      $id = Bdd::get_field_with_conditions("matched_conv", "id", "id_member_a = '" . $_SESSION['id_msg'] . "' AND id_member_b = '" . $_SESSION['id'] . "'");
    if (!isset($id[0]))
    {
      self::create_new_conv();
      return (self::get_conv_id());
    }
    return ($id[0]['id']);
  }

  private static function create_new_conv()
  {
    $bdd = new Bdd;
    $query = "INSERT INTO matcha.matched_conv(id, id_member_a, id_member_b, messages) VALUES(id, :id_member_a, :id_member_b, messages)";
    $instruct = $bdd->prepare($query);
    $instruct->bindParam(':id_member_a', $_SESSION['id'], PDO::PARAM_STR);
    $instruct->bindParam(':id_member_b', $_SESSION['id_msg'], PDO::PARAM_STR);
    $instruct->execute();
  }

  public static function get_default_user()
  {
    $matched = self::get_matched_users();
    if (!isset($matched[0]))
      return (-1);
    return ($matched[0]);
  }

  public static function get_user_pic($matched_user_picture)
  {
    if (isset($matched_user_picture[0]['images']))
      return (unserialize($matched_user_picture[0]['images']));
    else if (isset($matched_user_picture[0]))
      return ($matched_user_picture[0]);
    // $pict = (isset($matched_user_picture[0]['images'])) ? $matched_user_picture[0]['images'] : $matched_user_picture[0];
    // print($pict);
    // if (!$matched_user_picture && (empty($matched_user_picture[0])))
    //   $matched_user_picture = 'view/matcha_logo.png';
    // if (isset($pict))
    //   return (unserialize($pict));
    // if (is_array($matched_user_picture))
    //   return ('view/matcha_logo.png');
    return ($matched_user_picture);
  }

  public static function display_matched_users()
  {
    $matches = self::get_matched_users();
    $layout = new Layout;
    echo("<div class='col-sm-12'>");
    foreach($matches as $match)
    {
      echo("<div class='col-sm-12 row'>");
      $matched_user_login = Bdd::get_field_with_conditions("users", "login", "id = '" . $match . "'")[0]['login'];
      // $BAAA = Bdd::get_field_with_conditions("users_profile", "images", "id = '" . $match . "'");
      // print_r(unserialize(Bdd::get_field_with_conditions("users_profile", "images", "id = '" . $match . "'")[0]['images']));
      $matched_user_picture = self::get_user_pic(unserialize(Bdd::get_field_with_conditions("users_profile", "images", "id = '" . $match . "'")[0]['images']));
      echo("<div class='col-sm-1'></div>");
      // print($matched_user_picture);
      $notifs = bdd::get_field_with_conditions("notifications", "id_member_a",
      "id_member_b = " . $_SESSION['id'] .
      " AND is_new = 1 AND id_member_a = " . $match);
      echo("
      <a class='no-deco' href='index.php?p=messages&id=" . $match . "'>
      <div class='col-sm-11 row'>");
      echo("<img class='col-sm-5 mini_pic' src='" . $matched_user_picture . "'>");
      if (empty($notifs) || $_SESSION['id_msg'] == $match)
        echo("<p class='sub-title'>" . $matched_user_login . "</p>");
      else
      {
        echo("<div class='new_notif'>*</div>");
        echo("<p class='sub-title bold'>" . $matched_user_login . "</p>");
      }
      echo("</div></a></div>");
      $layout->white_space(1);
    }
    echo("</div>");
  }

  public static function to_array($old_array)
  {
    $new_array = array();
    if (is_array($old_array))
      foreach($old_array as $id)
        if (isset($id) && $id != NULL)
          array_push($new_array, $id);
    else
      $new_array[0] = $old_array;
    return ($new_array);
  }

  private static function is_in_array($array, $elem)
  {
      foreach($array as $arr)
          if ($arr == $elem)
            return (true);
      return (false);
  }

  public static function has_matched($id)
  {
    $matches = self::get_matched_users();
    return (self::is_in_array($matches, $id));
  }

  private static function is_connected_user_talking($msg)
  {
    if ($msg[0] == $_SESSION['id_msg'])
      return (false);
    return (true);
  }

  private static function not_connected_user_message($pic, $msg, $mini = null)
  {
    if (is_array($pic))
      $pic = $pic[0];
    echo("
    <div class='col-sm-12 row'>");
    if ($mini == null)
    {
    // echo("
    //   <div class='col-sm-4 clear_bg message'>" . $msg[1] . "</div>");
      // echo("<div class='col-sm-5'>");
        echo("<a class='col-sm-1 link-msg' href='index.php?p=member_profile&id=" . $_SESSION['id_msg'] . "'><img class='col-sm-12 message_pic' src='" . $pic . "'></a>");
      // echo("</div>");
      echo("<div class='col-sm-4 clear_bg message'>" . $msg[1] . "</div>");
    }
    else
    {
    echo("
      <div class='col-sm-12'>
      <img class='col-sm-3 message_pic' src='" . $pic . "'>
      <div class='col-sm-9 clear_bg message'>" . $msg[1] . "</div>");
      echo("</div>");
    }
    echo("</div>");
    echo("<br />");
  }

  private static function connected_user_message($pic, $msg, $mini = null)
  {
    if (is_array($pic))
      $pic = $pic[0];
    echo("
    <div class='col-sm-12 row'>");
    if ($mini == null)
    {
    echo("
      <div class='col-sm-7'></div>
      <div class='col-sm-4 clear_bg message'>" . $msg[1] . "</div>");
      // echo("<div class='col-sm-7'></div>");
      echo("<div class='col-sm-1 self-img-msg'>");
        // echo("<div class='col-sm-10'></div>");
        echo("<img class='col-sm-2 message_pic' src='" . $pic . "'>");
        echo("</div>");
    }
    else {
    echo("
      <div class='col-sm-9 clear_bg message'>" . $msg[1] . "</div>
        <img class='col-sm-3 message_pic' src='" . $pic . "'>
      ");
    }
    echo("</div>");
    echo("<br />");
  }

  public static function display_messages($id_msg = null)
  {
    if ($id_msg != null)
      $msgs = self::get_messages($id_msg);
    else
      $msgs = self::get_messages($_SESSION['id_msg']);
    $msgs = self::to_array(unserialize(base64_decode($msgs)));
    $matched_user_pic = self::get_user_pic(Bdd::get_field_with_conditions("users_profile", "images", "id='". $_SESSION['id_msg'] . "'"));
    $connected_user_pic =  self::get_user_pic(Bdd::get_field_with_conditions("users_profile", "images", "id='". $_SESSION['id'] . "'"));
    foreach($msgs as $msg)
    {
      echo("<div class='col-sm-12'>");
      if (!self::is_connected_user_talking($msg))
        self::not_connected_user_message($matched_user_pic, $msg, $id_msg);
      else
        self::connected_user_message($connected_user_pic, $msg, $id_msg);
      echo("</div>");
    }
    if (self::is_blocked_by_member())
      echo("<div class='error_message italic text-center'> Vous avez été bloqué par cet utilisateur</div>");
    if (self::has_blocked_member())
      echo("<div class='error_message italic text-center'> Vous avez bloqué cet utilisateur</div>");
  }

  public static function write_messages($mini = null)
  {
    $form = new Form("POST", "index.php?p=messages&id=" . $_SESSION['id_msg']);
    $rows = 3;
    $name = 'message';
    $content = '';
    echo("<div class='col-sm-12 row'>");
    echo("<textarea class='col-sm-10' rows='" . $rows . "' id='" . $name . "' name=" . $name . " placeholder=". $content ."
    ></textarea>");
          if ($mini != null)
            echo("<input class='col-sm-2' type  ='button'id='send' value='V'/>");
          else
            echo("<input class='col-sm-2' type  ='submit' value='Envoyer'/>
        </div>
      </form>
      </div>
    ");
    echo("</div>");
  }

  public static function count_new_msg_notif()
  {
    $count = Bdd::count_field("id", "notifications", "id_member_b = " . $_SESSION['id'] . " AND is_new = 1 AND notif = 'msg'");
    if ($count == "undefined")
      return ;
    if (intval($count) > 0)
      echo($count);
  }

  private static function send_notif()
  {
    $ret = Bdd::get_field_with_conditions("notifications", "*",
    "id_member_a = " . $_SESSION["id"] .
    " AND id_member_b = " . $_SESSION["id_msg"] .
    " AND is_new = 1
    AND notif = 'msg'");
    // if (!empty($ret))
    //   Bdd::del_notif($ret[0]['id']);
    Bdd::add_notif($_SESSION["id"], $_SESSION["id_msg"], "msg");
  }

  private static function is_blocked_by_member()
  {
    $blocked_id = self::to_array(unserialize(base64_decode(Bdd::get_user_field_id($_SESSION["id_msg"], "blocked_id", "users"))));
    foreach($blocked_id as $blocked)
      if ($blocked == $_SESSION['id'])
          return true;
    return false;
  }

  private static function has_blocked_member()
  {
    $blocked_id = self::to_array(unserialize(base64_decode(Bdd::get_user_field_id($_SESSION["id"], "blocked_id", "users"))));
    foreach($blocked_id as $blocked)
      if ($blocked == $_SESSION['id_msg'])
          return true;
    return false;
  }

  public static function send_message($msg)
  {
    if (self::is_blocked_by_member() || self::has_blocked_member())
      return ;
    $conv_id = self::get_conv_id();
    $new_message = array($_SESSION['id'], $msg);
    $old_messages = self::to_array(unserialize(base64_decode(Bdd::get_field_with_conditions("matched_conv", "messages", "id = " . $conv_id)[0]['messages'])));
    array_push($old_messages, $new_message);
    Bdd::alter_table($conv_id, "messages", $old_messages, "matched_conv", true);
    self::send_notif();
  }

  public static function erase_new_msg_notif()
  {
    $notifs = Bdd::get_field_with_conditions("notifications", "*",
    "id_member_b = " . $_SESSION["id"] .
    " AND id_member_a = " . $_SESSION["id_msg"] . "
    AND is_new = 1 AND notif = 'msg'", "DESC");
    foreach ($notifs as $n)
    {
      Bdd::alter_table($n['id'], "is_new", 0, "notifications");
    }
  }
}
?>
