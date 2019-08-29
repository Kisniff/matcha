<?php
Session_status::redirect_offline_user("Vous devez être connecté pour pouvoir effectuer une recherche avancee");
include_once("model/Form.php");
include_once("model/Members.php");
$layout->main_title("Recherche avancee", "<a href='index.php?p=extended_search'>Effectuer une nouvelle recherche</a>");

if (!isset($_POST['likes_min']) && !isset($_GET['page']))
{
  /*
  ** Displaying extended search formular
  */
  $form = new Form("POST", "index.php?p=extended_search");
  echo("
    <div class='col-sm-11'>");
  Form::interval("Tranche d'age", "age");
  $layout->white_space(1);
  Form::interval("Nombre de likes", "likes");
  $layout->white_space(1);
  $form->entry("Localisation", "text", "localisation");
  $form->entry("Tags", "text", "tags", null, "Séparez les tags par une virgule");
  $form->button("Rechercher !");
  echo("</div>");
}
else
{
  /*
  ** Treating formular datas
  */
  Form::get_extended_search_datas();
  if (!isset($_GET['page']) || !isset($_SESSION['profiles']))
    $profiles = Bdd::find_extended_search_profiles();
  else if (isset($_SESSION["profiles"]))
    $profiles = $_SESSION["profiles"];
  Layout::debug("");
  // print_r($profiles);

  /*
  ** Displaying users
  */
  /*
  ** defining needed functions
  */
  function get_users_with_idx($users, $idx_min, $pic_per_page)
  {
    $result = array();
    $go = 0;
    $id_min = $users[$idx_min]['id'];
    foreach ($users as $user)
    {
      if ($go <= $pic_per_page && ($user['id'] == $id_min || $go > 0))
      {
        ++$go;
        array_push($result, $user);
      }
    }
    return ($result);
  }

  function get_users_info($users, $id_min, $nb_users)
  {
    $users_info = Bdd::get_field_with_conditions("users", "*", "id >= " . $id_min);
    $i = -1;
    $result = array();
    while (isset($users[++$i]) && $i < $nb_users)
      array_push($result, $users_info[$i]);
    return ($result);
  }

  function get_users_profile($users, $id_min, $nb_users)
  {
    $users_info = Bdd::get_field_with_conditions("users_profile", "*", "id >= " . $id_min);
    $i = -1;
    $result = array();
    while (isset($users[++$i]) && $i < $nb_users)
      array_push($result, $users_info[$i]);
    return ($result);
  }

  /*
  ** defining loop variables
  */
  $pic_per_page = 6;
  $nb_users = count($profiles);
  $nb_pages = ceil($nb_users / $pic_per_page);
  if (isset($_GET['page']))
    $page = intval(htmlspecialchars($_GET['page']));
  else
    $page = 0;
  /*
  ** getting users with appropriate id
  */
  $idx_min = $pic_per_page * $page;
  if ($idx_min > $nb_users)
    $idx_min = 0;
  $i = -1;
  if (!(isset($profiles) && count($profiles) > 0))
    $layout->main_error("Aucun utilisateur ne correspond a votre recherche");
  else
    $_SESSION["profiles"] = $profiles;
  $to_display = get_users_with_idx($profiles, $idx_min, $pic_per_page);
  $users_profile = get_users_profile($profiles, $profiles[$idx_min]['id'], count($to_display));
  $users_info = get_users_info($profiles, $profiles[$idx_min]['id'], count($to_display));
  Members::display_user($users_profile, $users_info, 0, "extended");
  Members::display_pagination($page, $nb_pages, "index.php?p=extended_search&page=");
}
?>
<script>
var screenWidth = window.innerWidth;
var imgs = document.getElementsByClassName('photo');
for(var i = 0; i < imgs.length; i++)
{
  imgs[i].style.height = screenWidth / 6 +'px';
  imgs[i].style.width = 'auto';
}
</script>
