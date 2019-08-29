<?php
class Notifs{
  public static function count_new_notifs()
  {
    $count = Bdd::count_field("id", "notifications", "id_member_b = " . $_SESSION['id'] . " AND is_new = 1 AND notif != 'msg'");
    if ($count == "undefined")
      return ;
    if (intval($count) > 0)
      echo($count);
  }

  private static function handle_no_notifs($notifs)
  {
    if (empty($notifs))
    {
      echo("<div class='col-sm-12 error_message text-center'>Vous n'avez pas encore de notifications !</div>");
      echo("<br />");
      echo("<div class='col-sm-12 sub-title text-center'><a href='index.php?p=members'>Rendez-vous dans la section Membres pour découvrir des profils</a></div>");
      exit ;
    }
  }

  private static function format_notif($n, $user_a, $notif)
  {
    if ($n['is_new'] == 1)
    {
      echo("<div class='col-sm-12 text-center green'><a href=index.php?p=member_profile&id=" . $n['id_member_a'] . ">" . $user_a . "</a> " . $notif . " </div>");
      echo("<div class='new_notif'>*</div>");
      return ;
    }
    echo("<div class='col-sm-12 text-center'><a href=index.php?p=member_profile&id=" . $n['id_member_a'] . ">" . $user_a . "</a> " . $notif . "</div>");
  }

  private static function write_notif($n, $user_a)
  {
      echo("<div class='col-sm-12 row'>");
      echo("<div class='col-sm-4'></div>");
      echo("<div class='col-sm-4 dark_bg'>");
      if ($n["notif"] == "visit")
        self::format_notif($n, $user_a, "a visité votre profil");
      if ($n["notif"] == "like")
        self::format_notif($n, $user_a, "a liké votre profil");
      if ($n["notif"] == "unlike")
        self::format_notif($n, $user_a, "a unliké votre profil");
      if ($n["notif"] == "match")
        self::format_notif($n, $user_a, "vous a matché !");
      echo("</div>");
      echo("</div>");
      echo("<br />");
  }

  private static function erase_new_notifs()
  {
    $notifs = Bdd::get_field_with_conditions("notifications", "*",
    "id_member_b = " . $_SESSION["id"] .
    " AND is_new = 1 AND notif != 'msg'", "DESC");
    foreach ($notifs as $n)
    {
      Bdd::alter_table($n['id'], "is_new", 0, "notifications");
    }
  }

  public static function display_notifs()
  {
    $notifs = Bdd::get_field_with_conditions("notifications", "*",
    "id_member_b = " . $_SESSION["id"], "DESC");
    self::handle_no_notifs($notifs);
    $notifs = $notifs;
    foreach ($notifs as $n)
    {
      $user_a = Bdd::get_user_field_id($n['id_member_a'], 'login');
      self::write_notif($n, $user_a);
    }
    self::erase_new_notifs();
  }
}
?>
