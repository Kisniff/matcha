<?php
class Layout
{
    public function white_space($size)
    {
      echo("<div>");
      $i = -1;
      while (++$i < $size)
        echo("<br />");
      echo("</div>");
    }

    public function sub_title($sub_title, $space = 2, $class = null)
    {
        if (!$class)
          $class = "text-center";
        echo('
        <div class="container col-sm-12">
          <div class="row">
            <div class="col-sm-12"><p class="sub-title ' . $class . '">' . $sub_title . '</p></div>
          </div>
        </div>
        ');
        $this->white_space($space);
    }

    public function main_title($title, $sub_title = null)
    {
      if ($title == "Photos")
        $this->white_space(2);
      echo('
        <div class="container col-sm-12">
          <div class="row">
            <div class="col-sm-12"><h1 class="text-center">' . $title . '</h1></div>
          </div>
        </div>
      ');
      if ($sub_title == null)
        $this->white_space(3);
      else
        $this->sub_title($sub_title, 0);
    }

    public function nav_bar()
    {
      if (isset($_SESSION['connexion_status']) && $_SESSION['connexion_status'] == "connected")
      {
        $login_var = "Deconnexion";
        $nb_field = 4;
      }
      else
      {
        $login_var = "Connexion";
        $nb_field = 3;
      }
      $size = $nb_field / 10;
      echo('
        <div class="container col-sm-12">
          <div class="row navbar">
            <div class="col-sm-2">
              <div class="col-sm-1"></div>
              <a href="index.php"><img class="col-sm-4 matcha" src="view/matcha_logo_navbar.png"/></a>
            </div>
            <div class="col-sm-'. $size .' text-center"><a href="index.php" class="text-center">Accueil</a></div>
            <div class="col-sm-'. $size .' text-center"><a href="index.php?p=members" class="text-center">Membres</a></div>
            ');
          if ($login_var == "Deconnexion")
          {
            echo('<div class="col-sm-'. $size .' text-center"><a href="index.php?p=account_settings" class="text-center">Espace privé</a></div>');
          }
          echo('<div class="col-sm-'. $size .' text-center"><a href="index.php?p=' . $login_var . '" class="text-center">' . $login_var . '</a></div>');
          echo('
            <div class="col-sm-3 row">');
          if ($login_var == "Deconnexion")
          {
              echo('<div class="col-xs-4 row icone-ring"><a id="link" href="index.php?p=notifs">');
                echo('<img class="col-sm-12 icone-nav" src="view/bell.png"/>');
                echo('<div id="notif"></div>');
              echo('</a></div>');
              // echo('<div class="col-sm-2"></div>');
              echo('<div class="col-xs-4"><a id="link" href="index.php?p=messages">
              <img class="col-sm-12 icone-nav" src="view/mail_logo_navbar.png"/>
              <div id="msg"></div>
              </a></div>');
          }
          echo('
            </div>
          </div>
        </div>
      ');
      $this->white_space(3);
    }

    public function footer()
    {
      echo('
      <footer class="navbar-fixed-bottom text-center">
        Ce footer ne sert a rien ©
      </footer>
      ');
    }

    public function main_error($message = null)
    {
      $this->white_space(3);
      echo("
      <div class='container col-sm-12'>
        <p class='col-sm-12 error_message text-center'>Erreur : " . $message . "</p>");
      $this->white_space(1);
      echo("
        <p class='col-sm-12 text-center'><a href='index.php'> Retour a la page d'accueil</a></p>
      </div>
      ");
      exit ;
    }

    public static function connection_restricted_area()
    {
       if ($_SESSION['connexion_status'] != "connected")
       {
         (new self)->main_error("Vous devez être connecté pour pouvoir accéder a cette page");
         exit;
       }
    }

    public static function debug($str)
    {
      echo("<br />" . $str . "<br />");
    }
}
?>
