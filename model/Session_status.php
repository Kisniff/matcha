<?php
Class Session_status
{
  public static function page_handler()
  {
    if (isset($_GET['p']))
      $_SESSION['page'] = htmlspecialchars($_GET['p']);
    else
      $_SESSION['page'] = "home";
  }

  public static function is_connected()
  {
    if (!isset($_SESSION['connexion_status'])
    || $_SESSION['connexion_status'] == 'offline'
    || ( $_SESSION['connexion_status'] == 'attempt'
    && $_SESSION['page'] != 'subscribe'
    && $_SESSION['page'] != 'connexion_landing_page'
    && $_SESSION['page'] != 'Connexion')
    || $_SESSION['connexion_status'] == 'offline')
      return false;
    return true;
  }

  public static function connexion_status()
  {
    if (!isset($_SESSION['connexion_status']))
      $_SESSION['connexion_status'] = 'offline';
    if ($_SESSION['connexion_status'] == 'attempt'
    && $_SESSION['page'] != 'subscribe'
    && $_SESSION['page'] != 'connexion_landing_page'
    && $_SESSION['page'] != 'Connexion')
      $_SESSION['connexion_status'] = 'offline';
    if ($_SESSION['page'] == "Deconnexion")
      $_SESSION['connexion_status'] = 'offline';
  }

  public static function redirect_offline_user($message)
  {
    $layout = new layout;
    if ($_SESSION['connexion_status'] != "connected")
    {
      $layout->main_error($message);
      exit;
    }
  }
}
?>
