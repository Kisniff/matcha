<?php
class Member_profile
{
  public static function does_member_exist()
  {
    $layout = new Layout;
    if (!($ret = Bdd::is_id_valid($_SESSION["member_id"], "users")))
    {
      $layout->white_space(3);
      echo("<p class='col-sm-12 error_message text-center'>L'utilisateur que vous cherchez n'existe pas</p>");
      exit ;
    }
  }

  private static function display_likes()
  {
    $likes = unserialize(base64_decode(Bdd::get_user_field_id($_SESSION["member_id"], "likes", "users")));
    $users_likes = Bdd::get_field_with_conditions("users", "id_liked", "id = '" . $_SESSION['id'] . "'");
    $old_liked_users_id = null;
    if (isset($users_likes[0])) {
      $old_liked_users_id = unserialize(base64_decode($users_likes[0]['id_liked']));
    }
    $liked_users_id = self::to_array($old_liked_users_id);
    if (!isset($likes) || $likes == 0)
      $likes = 0;
    else
      $likes = count($likes);
    if ($_SESSION["id"] != $_SESSION["member_id"])
    {
      if (!self::can_like_user($liked_users_id))
        echo("<div class='col-sm-2 green'>" . $likes . "<a href='index.php?p=member_profile&id=" . $_SESSION['member_id'] . "&action=like'><img width='30' src='view/thumbs-up.png'></a></div>");
      else
        echo("<div class='col-sm-2 green'>" . $likes . "<a href='index.php?p=member_profile&id=" . $_SESSION['member_id'] . "&action=like'><img width='30' src='view/empty_thumbs_up.png'></a></div>");
    }
    else
      echo("<div class='col-sm-2 green'>" . $likes . "<img width='30' src='view/thumbs-up.png'></div>");
  }

  private static function can_block_user($blocked_users_id)
  {
    foreach($blocked_users_id as $blocked)
      if ($blocked == $_SESSION["member_id"])
        return (false);
    return (true);
  }

  private static function display_block()
  {
    $blocked_id = unserialize(base64_decode(Bdd::get_user_field_id($_SESSION["id"], "blocked_id", "users")));
    $blocked_id = self::to_array($blocked_id);
    if ($_SESSION["id"] != $_SESSION["member_id"])
    {
      if (!self::can_block_user($blocked_id))
        echo("<div class='col-sm-2'><a href='index.php?p=member_profile&id=" . $_SESSION['member_id'] . "&action=block'><img width='30' src='view/blocked.png' alt='déblocker l'utilisateur'></a></div>");
      else
        echo("<div class='col-sm-2'><a href='index.php?p=member_profile&id=" . $_SESSION['member_id'] . "&action=block'><img width='30' src='view/to_block.png' alt='blocker l'utilisateur'></a></div>");
    }
  }

  private static function can_report_user($reported_by_id)
  {
    foreach($reported_by_id as $report)
      if ($report == $_SESSION["id"])
        return (false);
    return (true);
  }

  private static function display_report()
  {
    $reported_by_id = self::to_array(unserialize(base64_decode(Bdd::get_user_field_id($_SESSION["member_id"], "reported_by_id", "users"))));
    if ($_SESSION["id"] != $_SESSION["member_id"])
    {
      if (!self::can_report_user($reported_by_id))
        echo("<div class='col-sm-2'><a href='index.php?p=member_profile&id=" . $_SESSION['member_id'] . "&action=report'><img width='30' src='view/reported.png' alt='déblocker l'utilisateur'></a></div>");
      else
        echo("<div class='col-sm-2'><a href='index.php?p=member_profile&id=" . $_SESSION['member_id'] . "&action=report'><img width='30' src='view/to_report.png' alt='blocker l'utilisateur'></a></div>");
    }
  }

  private static function has_liked()
  {
    $liked = unserialize(base64_decode(Bdd::get_user_field_id($_SESSION["member_id"], "id_liked", "users")));
    if (is_array($liked))
      foreach($liked as $like)
        if ($like == $_SESSION['id'])
          return (true);
    else
      if ($liked == $_SESSION['id'])
        return (true);
    return (false);
  }

  public static function display_profile_name_likes_block_report()
  {
    $layout = new Layout;
    $login = Bdd::get_user_field_id($_SESSION["member_id"], "login", "users");
    $tags = Bdd::get_user_field_id($_SESSION["member_id"], "tags", "users_profile");
    echo("<div class='col-sm-12 row'>
      <h1 class='col-sm-6 text-left'>" . $login . "</h1>");
    self::display_likes();
    self::display_block();
    self::display_report();
    echo("</div>");
    echo("<p class='col-sm-12 text-left sub-title'>" . $tags . "</p>");
  }

  private static function display_profile_picture()
  {
    if (!($imgs = unserialize(Bdd::get_user_field_id($_SESSION["member_id"], "images", "users_profile"))))
      $profile_pic = "view/matcha_logo.png";
    else
      $profile_pic = (is_array($imgs)) ? $imgs[0] : $imgs;
    echo("<img class='col-sm-12 pic photo' src='" . $profile_pic . "'/>");
  }

  private static function display_bio()
  {
    $bio = Bdd::get_user_field_id($_SESSION["member_id"], "biographie", "users_profile");
    if ($bio)
      echo("<p class='col-sm-12 sub-title'>\" " . $bio . " \"</p>");
    if (self::has_liked())
      echo("<div class='col-sm-12 text-right green italic'>Ce profil vous a liké !</div>");
  }

  private static function display_name_orientation_gender()
  {
    $orientation = Bdd::get_user_field_id($_SESSION["member_id"], "orientation", "users_profile");
    $gender = Bdd::get_user_field_id($_SESSION["member_id"], "genre", "users_profile");
    $first_name = Bdd::get_user_field_id($_SESSION["member_id"], "first_name", "users");
    $last_name = Bdd::get_user_field_id($_SESSION["member_id"], "last_name", "users");
    echo("<div class='col-sm-1'></div>");
    echo("<div class='col-sm-11'>" . $first_name . " " . $last_name . ", <span class='green italic'>" . $gender . " et " . $orientation . "</span></div>");
  }

  private static function display_other_pictures()
  {
    if (($imgs = unserialize(Bdd::get_user_field_id($_SESSION["member_id"], "images", "users_profile"))))
      if (is_array($imgs))
      {
        echo("<div class='col-sm-12 row'>");
        for ($i = 1; $i < count($imgs); $i++)
        {
          echo("
              <img class='col-sm-2 pic' src='" . $imgs[$i] . "'/>
          ");
        }
        echo("</div>");
      }
  }

  public static function display_user()
  {
    echo("
    <div class='col-sm-12 row'>
      <div class='col-sm-5'>");
      self::display_profile_picture();
      echo("</div>
      <div class='col-sm-7'>");
      self::display_profile_name_likes_block_report();
      self::display_bio();
      echo("</div>");
      echo("<div class='col-sm-5 row'>");
      self::display_name_orientation_gender();
      echo("</div>");
    echo("</div>");
    self::display_other_pictures();
  }

  private static function can_like_user($liked_users_id)
  {
    foreach($liked_users_id as $id)
      if ($id == $_SESSION["member_id"])
        return (false);
    return (true);
  }

  private static function to_array($old_array)
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

  private static function delete_from_array($to_del, $array)
  {
    $i = -1;
    while (isset($array[++$i]))
      if ($array[$i] == $to_del)
        unset($array[$i]);
    return ($array);
  }

  private static function add_match($user_id, $matched_id)
  {
    $matches = self::to_array(unserialize(base64_decode(Bdd::get_field_with_conditions("users", "matches", "id = '" . $user_id . "'")[0]['matches'])));
    if (isset($matches['matches']))
      $matches = $matches['matches'];
    foreach($matches as $id)
      if ($id == $matched_id)
        return ;
    array_push($matches, $matched_id);
    Bdd::alter_table($user_id, "matches", base64_encode(serialize($matches)));
  }

  private static function del_match($user_id, $matched_id)
  {
    $matches = self::to_array(unserialize(base64_decode(Bdd::get_field_with_conditions("users", "matches", "id = '" . $user_id . "'")[0]['matches'])));
    if (isset($matches['matches']))
      $matches = $matches['matches'];
    $i = -1;
    while (++$i < count($matches))
      if ($matches[$i] == $matched_id)
        unset($matches[$i]);
    Bdd::alter_table($user_id, "matches", base64_encode(serialize($matches)));
  }

  private static function handle_match()
  {
    $consulted_profile_likes = unserialize(base64_decode(Bdd::get_field_with_conditions("users", "id_liked", "id = '" . $_SESSION['member_id'] . "'")[0]['id_liked']));
    $consulted_profile_likes = self::to_array($consulted_profile_likes);
    foreach($consulted_profile_likes as $id)
    {
      if ($id == $_SESSION['id'])
      {
          self::add_match($_SESSION['id'], $_SESSION['member_id']);
          self::add_match($_SESSION['member_id'], $_SESSION['id']);
      }
    }
  }

  private static function is_blocked_by_member()
  {
    $blocked_id = self::to_array(unserialize(base64_decode(Bdd::get_user_field_id($_SESSION["member_id"], "blocked_id", "users"))));
    foreach($blocked_id as $blocked)
      if ($blocked == $_SESSION['id'])
          return true;
    return false;
  }

  private static function send_like_notif()
  {
    if (self::is_blocked_by_member())
      return ;
    $ret = Bdd::get_field_with_conditions("notifications", "*",
    "id_member_a = " . $_SESSION["id"] .
    " AND id_member_b = " . $_SESSION["member_id"] .
    " AND notif = 'like'");
    if (!empty($ret))
      Bdd::del_notif($ret[0]['id']);
    if (self::has_liked())
    {
      $ret = Bdd::get_field_with_conditions("notifications", "*",
      "id_member_a = " . $_SESSION["id"] .
      " AND id_member_b = " . $_SESSION["member_id"] .
      " AND is_new = 1
      AND notif = 'match'");
      if (!empty($ret))
        Bdd::del_notif($ret[0]['id']);
      Bdd::add_notif($_SESSION["id"], $_SESSION["member_id"], "match");
    }
    else
      Bdd::add_notif($_SESSION["id"], $_SESSION["member_id"], "like");
  }

  private static function send_unlike_notif()
  {
    if (self::is_blocked_by_member())
      return ;
    $ret = Bdd::get_field_with_conditions("notifications", "*",
    "id_member_a = " . $_SESSION["id"] .
    " AND id_member_b = " . $_SESSION["member_id"] .
    " AND is_new = 1
    AND notif = 'unlike'");
    if (!empty($ret))
      Bdd::del_notif($ret[0]['id']);
    $ret = Bdd::get_field_with_conditions("notifications", "*",
    "id_member_a = " . $_SESSION["id"] .
    " AND id_member_b = " . $_SESSION["member_id"] .
    " AND is_new = 1
    AND notif = 'like'");
    if (!empty($ret))
        Bdd::del_notif($ret[0]['id']);
    Bdd::add_notif($_SESSION["id"], $_SESSION["member_id"], "unlike");
  }

  public static function report_user()
  {
    $reported_by_id = self::to_array(unserialize(base64_decode(Bdd::get_field_with_conditions("users", "reported_by_id", "id = '" . $_SESSION['member_id'] . "'")[0]['reported_by_id'])));
    if (self::can_report_user($reported_by_id))
      array_push($reported_by_id, $_SESSION['id']);
    else
      $reported_by_id = self::delete_from_array($_SESSION['id'], $reported_by_id);
    Bdd::alter_table($_SESSION['member_id'], "reported_by_id", base64_encode(serialize($reported_by_id)));
  }

  public static function block_user()
  {
    $blocked_users = self::to_array(unserialize(base64_decode(Bdd::get_field_with_conditions("users", "blocked_id", "id = '" . $_SESSION['id'] . "'")[0]['blocked_id'])));
    if (self::can_block_user($blocked_users))
      array_push($blocked_users, $_SESSION['member_id']);
    else
      $blocked_users = self::delete_from_array($_SESSION['member_id'], $blocked_users);
    Bdd::alter_table($_SESSION['id'], "blocked_id", base64_encode(serialize($blocked_users)));
  }

  public static function like_user()
  {
    if (!($imgs = unserialize(Bdd::get_user_field_id($_SESSION["id"], "images", "users_profile"))))
    {
      echo("<p class='error_message italic text-center'>Vous ne pouvez pas liker d'utilisateurs tant que vous n'avez pas de photos de profil.</p>");
      return ;
    }
    $old_liked_users_id = unserialize(base64_decode(Bdd::get_field_with_conditions("users", "id_liked", "id = '" . $_SESSION['id'] . "'")[0]['id_liked']));
    $old_users_likes = unserialize(base64_decode(Bdd::get_field_with_conditions("users", "likes", "id = '" . $_SESSION['member_id'] . "'")[0]['likes']));
    $liked_users_id = self::to_array($old_liked_users_id);
    $users_likes = self::to_array($old_users_likes);
    if (self::can_like_user($liked_users_id))
    {
      array_push($liked_users_id, $_SESSION["member_id"]);
      array_push($users_likes, $_SESSION["id"]);
      Bdd::alter_table($_SESSION['id'], "id_liked", base64_encode(serialize($liked_users_id)));
      Bdd::alter_table($_SESSION['member_id'], "likes", base64_encode(serialize($users_likes)));
      Bdd::alter_table($_SESSION['member_id'], "likes_nb", count($users_likes));
      self::handle_match();
      self::send_like_notif();
    }
    else
    {
      $liked_users_id = self::delete_from_array($_SESSION['member_id'], $liked_users_id);
      $users_likes = self::delete_from_array($_SESSION['id'], $users_likes);
      Bdd::alter_table($_SESSION['id'], "id_liked", base64_encode(serialize($liked_users_id)));
      Bdd::alter_table($_SESSION['member_id'], "likes", base64_encode(serialize($users_likes)));
      Bdd::alter_table($_SESSION['member_id'], "likes_nb", count($users_likes));
      self::del_match($_SESSION['member_id'], $_SESSION['id']);
      self::del_match($_SESSION['id'], $_SESSION['member_id']);
      self::send_unlike_notif();
    }
  }

  /*
  ** send_notif
  ** Sends a notification each time a user visit an other user
  */
  public static function send_notif()
  {
    if (self::is_blocked_by_member())
      return ;
    $ret = Bdd::get_field_with_conditions("notifications", "*",
    "id_member_a = " . $_SESSION["id"] .
    " AND id_member_b = " . $_SESSION["member_id"] .
    " AND notif = 'visit'");
    if (!empty($ret))
      Bdd::del_notif($ret[0]['id']);
    Bdd::add_notif($_SESSION["id"], $_SESSION["member_id"], "visit");
  }
}

?>
