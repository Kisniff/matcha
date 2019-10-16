<?php
Class Members{

    private static function fill_image_array($users_profile, $users_info)
    {
      $nb_photos_to_display = (count($users_profile));
      $i = -1;
      $images = array();
      while (++$i < $nb_photos_to_display)
      {
        $pic = unserialize($users_profile[$i]['images']);
        if (!($pic))
          $pic = "view/matcha_logo.png";
        if (is_array($pic))
          $pic = $pic[0];
        array_push($images, $pic);
      }
      return ($images);
    }

    private static function display_photos($nb_photos_to_display, $images, $j, $id_start, $users_profile, $type, $users_info)
    {
        echo("
        <div class='col-sm-12 row'>
        <table class='col-sm-12 container'>
        <tr class='col-sm-12 row'>
          <th class='col-sm-1'></th>
        ");
        $i = -1;
        while (++$i < $nb_photos_to_display)
        {
          echo("
          <th class='col-sm-3 pic text-center'>");
          if ($type == null)
            echo("<a class='col-sm-12' href='index.php?p=member_profile&id=" . ($id_start + $i + $j) . "'>");
          else
            echo("<a class='col-sm-12' href='index.php?p=member_profile&id=" . $users_profile[$id_start + $i + $j]['id'] . "'>");
          echo("
              <img class='col-sm-12 rounded photo' src='" . $images[$i + $j] . "'/>
              <figcaption class='pictcaption'>".$users_info[$i]['login']."</figcaption>
            </a>
          </th>
          ");
        }
        echo("</tr>");
    }

    private static function display_logins($nb_photos_to_display, $users_info, $j)
    {
      $layout = new Layout;
      $i = -1;
      echo("<tr class='col-sm-12 row'>
          <th class='col-sm-1'></th>");
      while (++$i < $nb_photos_to_display)
      {
        echo("
        <th class='col-sm-3 login_likes'>
        <div class='col-sm-12 text-center'>" . $users_info[$i + $j]['login'] . "</div>
        </div>
        </th>
        ");
      }
      echo("</tr>");
    }

    private static function display_profils($profils, $filtres = NULL) {

      //$filtres = array (filtres demandé dans la recherche avancée)
      // $order = booleen (ordonné si connecté)

      $count = count($profils);
      $i = 0;
      // print($count);
      echo("
          <div class='container_profils'>");
      while ($i < $count) {
        $id = $profils[$i]['id'];
        $profil_pict = unserialize($profils[$i]['images'])[0] ? unserialize($profils[$i]['images'])[0] : "view/matcha_logo.png";
        $login = Bdd::get_login($id)[0];
        echo("<div class='item_profil'>");
          echo("<a style='text-decoration:none' href='index.php?p=member_profile&id=" . $id . "'>");
          echo("
              <img class='photo_profil' src='" . $profil_pict . "'/>
              <figcaption class='pictcaption'>".$login."</figcaption>
            </a>");
          echo("</div>");
        $i++;
      }
      echo("</div>");
    }

    public static function display_user($page)
    {
      $layout = new Layout;
      $offset = $page * 6;

      // print_r($_SESSION);
      
      if (isset($_SESSION)) {
        if ($_SESSION['connexion_status'] == 'offline') {
          // pas d'ordres
          // pas de filtres
        }
        else {
          $user_infos = Bdd::get_user_profil($_SESSION['id'], '*');
          // print_r($user_infos);
          $orientation_user = $user_infos['orientation'];
          $lat = $user_infos['latitude'];
          $long = $user_infos['longitude'];
          $query = 'SELECT * FROM matcha.`users_profile` WHERE `id` != '.$_SESSION['id'].' ORDER BY case `orientation` WHEN "'.$orientation_user.'" then 1 else 2 end, `orientation`, ABS('.$lat.' - latitude) ASC, ABS('.$long.' - longitude) ASC LIMIT 6 OFFSET '.$offset;
          $ordered_profils = Bdd::order_profils($query);
          // print_r($ordered_profils);
          self::display_profils($ordered_profils);
        }
      }
      
      // $nb_photos_to_display = (count($users_profile));
      // print($nb_photos_to_display);
      // $countdown =  count($users_profile) - 1;
      // if ($countdown <= 0)
      //   $countdown = 1;
      // else $countdown = $nb_photos_to_display;
      // $images = self::fill_image_array($users_profile, $users_info);
      // $nb_photos_to_display = ($countdown) > 1 ? 3 : $countdown; // pourquoi ?
      // $j = 0;
      // $i = -1;
      
      // while ($nb_photos_to_display > 0)
      // {
        // self::display_photos($nb_photos_to_display, $images, $j, $id_start, $users_profile, $type, $users_info);
        // self::display_profils($nb_photos_to_display, $images, $j, $id_start, $users_profile, $type, $users_info);

        // $layout->white_space(2);
        // $j += $nb_photos_to_display;
        // $countdown -= $nb_photos_to_display;
        // $nb_photos_to_display = $countdown;
        // $nb_photos_to_display--;
      // }
      
    }

    public static function display_pagination($page, $nb_pages, $url = "index.php?p=members&page=")
    {
      $prev_page = ($page > 0) ? $page - 1 : $nb_pages - 1;
      $next_page = ($page < $nb_pages - 1) ? $page + 1 : 0;
      echo('
      <div class="col-sm-12 row">
        <div class="col-sm-3"></div>
        <a class="col-sm-2" href="' . $url . $prev_page . '"><<</a>
        <a class="col-sm-2" href="' . $url . '0">1</a>
      ');
      if ($nb_pages - 1 > 0)
      echo('
        <a class="col-sm-2" href="' . $url . ($nb_pages - 1) .'">' . ($nb_pages - 1) . '</a>');
        echo('
        <a class="col-sm-2" href="' . $url . $next_page . '">>></a>
      </div>
      ');
    }

    public static function searching_menu()
    {//en test plus qu'autre chose pour l'instant
      echo("<p>Recherche avancee</p>");
      $form = new Form('index.php?p=members&search=ok', "POST");
      // $form->entry('login', );
      echo("
      <input type='range' min='1' max='100' value='50'>"
      );
      echo("</div>");
      echo("</div>");
    }

    private static function get_user_login($id)
    {
      $login = Bdd::get_user_field_id($id, "login");
      echo("<div class='col-sm-12 text-center'><a href='index.php?p=member_profile&id=" . $id . "'> " . $login . "</a></div>");
    }

    public static function display_visitors()
    {
        echo("<div classs='col-sm-12'>");
        $visitors = Bdd::get_field_with_conditions("notifications", "id_member_a", "notif = 'visit' AND id_member_b= " . $_SESSION['id']);
        if (empty($visitors))
        {
          echo("<div class='col-sm-12 error_message text-center'>Vous n'avez pas encore de visiteurs !</div>");
          echo("<br />");
          echo("<div class='col-sm-12 sub-title text-center'><a href='index.php?p=members'>Rendez-vous dans la section Membres pour découvrir des profils</a>");
          exit ;
        }
        foreach($visitors as $v)
        {
          self::get_user_login($v['id_member_a']);
          echo("<br />");
        }
        echo("</div>");
    }

    public static function display_likers()
    {
        echo("<div classs='col-sm-12'>");
        $likers = Bdd::get_field_with_conditions("notifications", "id_member_a", "notif = 'like' AND id_member_b= " . $_SESSION['id']);
        $matchers = Bdd::get_field_with_conditions("notifications", "id_member_a", "notif = 'match' AND id_member_b= " . $_SESSION['id']);
        if (empty($likers) && empty($matchers))
        {
          echo("<div class='col-sm-12 error_message text-center'>Vous n'avez pas encore de like !</div>");
          echo("<br />");
          echo("<div class='col-sm-12 sub-title text-center'><a href='index.php?p=members'>Rendez-vous dans la section Membres pour découvrir des profils</a>");
          exit ;
        }
        foreach($likers as $v)
        {
          self::get_user_login($v['id_member_a']);
          echo("<br />");
        }
        foreach($matchers as $v)
        {
          self::get_user_login($v['id_member_a']);
          echo("<br />");
        }
        echo("</div>");
    }
}
?>
