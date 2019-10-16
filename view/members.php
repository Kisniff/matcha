<?php
include_once("model/Members.php");
include_once("model/Form.php");

/*
** Defining loop variables
*/
// $pic_per_page = 6;
$bdd = new Bdd;
$nb_users = Bdd::count_field("id", "users");
$nb_pages = ceil($nb_users / 6);
if (isset($_GET['page']))
  $page = intval(htmlspecialchars($_GET['page']));
else
  $page = 0;

/*
** Getting users with appropriate id
*/
// $id_min = ($page == 0) ? 1 : $page * 6 + 1;
// $id_max = ($page + 1) * 6 + 2;
// $users_info = Bdd::get_field_with_conditions("users", "*", "id >= " . $id_min . " && id < " . $id_max);
// $users_profile = Bdd::get_field_with_conditions("users_profile", "*",  "id >= " . $id_min . " && id < " . $id_max);

/*
** Extended search
*/
echo("<div class='col-sm-12 text-center sub-title'><a href='index.php?p=extended_search'>Passer en mode recherche avancee</a></div>");

/*
** Displaying users
*/
Members::display_user($page);

/*
** Pagination
*/
Members::display_pagination($page, $nb_pages);

echo("</div>");
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
