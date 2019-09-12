<?php

include_once("model/Form.php");
Layout::connection_restricted_area();

/*
** Formulaire de modification des infos de profil
*/
$layout->main_title("Gestion du profil publique");
$form = new Form("post", "index.php?p=modif_user_profile");
$form->select("Genre",   array("non-binaire", "homme cisgenre", "femme cisgenre", "homme trans",
"femme trans", "genderfluid"));
$form->select("Orientation",   array("pansexuel.le", "bisexuel.le", "asexuel.le",
"homosexuel.le", "hétérosexuel.le"));
$form->textarea("Biographie", "Décrivez-vous ici ... ;)");

/*
** on va essayer de retrouver notre ip
*/
echo("<script>
function updateIp(response, status)
{
  alert('updateIp');
  var reponse = JSON.Parse(xmlhttp.response);
  if (reponse[ip])
  var ip = reponse[ip];
  return ip;
}



function getIp()
{
  var addr = 'http://www.geoplugin.com/';
  var request = $.ajax({
    beforeSend: function(xhr){xhr.setRequestHeader('Access-Control-Allow-Origin', 'http://www.geoplugin.com/');},
    url:addr,
    type:'GET',
    dataType:'json'
  });
  var ip = request.done(updateIp);
}

getIp();
</script>");

$form->entry("Tags", "text", "tags", null, "Séparez vos tags par une virgule");
echo("
<div class='row'>
<div class='col-sm-5'></div>
<div class='col-sm-7' id='tag'></div>
</div>
");
$layout->white_space(1);
$form->button("Actualiser !");

$id = Bdd::get_user_field($_SESSION['email'], "id");

Form::modif_profile_vars($id);

/*
** Upload de photos
*/
$layout->main_title("Photos", "Uploadez ici les photos de votre profil");
Form::file("photo", "index.php?p=upload");
if (isset($_FILES['photo']))
Form::download_file($_FILES['photo'], $id);

/*
**  Suppression des photos
*/
if ($_SESSION["page"] == "del_pic" && isset($_GET["id"]))
{
  Bdd::del_picture($id, htmlspecialchars($_GET["id"]));
}

/*
** Affichage des photos
*/
if (($pics = Bdd::get_user_field_id($id, "images", "users_profile")))
{
  $pics = unserialize($pics);
  echo("<div class='row col-sm-12'>");
  $i = -1;
  if (is_array($pics))
  foreach($pics as $pic)
  {
    $i++;
    echo("<table class='col-sm-2'>");
    echo("<tr>");
    echo("<th><img class='col-sm-12' src='" . $pic . "'/></th>");
    echo("</tr>");
    echo("<tr>");
    echo("<th class='text-right'><a href=index.php?p=del_pic&id=" . $i . " class='col-sm-4'><img width='10%' id='" . $i . "' src='view/red_cross.png'></a></th>");
    echo("</tr>");
    echo("</table>");
  }
  else
  {
    echo("<table class='col-sm-2'>");
    echo("<tr>");
    echo("<th><img class='col-sm-12' src='" . $pics . "'/></th>");
    echo("</tr>");
    echo("<tr>");
    echo("<th class='text-right'><a href=index.php?p=del_pic&id=0 class='col-sm-4'><img width='10%' id=0 src='view/red_cross.png'></a></th>");
    echo("</tr>");
    echo("</table>");
  }
  echo("</div>");
}

/*
**  Suppression des tags
*/
$tags = Bdd::get_user_field_id($id, "tags", "users_profile");
if ($_SESSION["page"] == "del" && isset($_GET["tag"]))
{
  $del_tag = htmlspecialchars($_GET['tag']);
  $del_tag = preg_replace('/[\x00-\x1F\x7F-\xA0\xAD]/u', '', $del_tag);
  $len_del = strlen($del_tag);
}
if (isset($del_tag))
{
  $tags = explode(", ", $tags);
  $i = -1;
  $tags_nb = count($tags);
  while(++$i < $tags_nb)
  {
    $to_compare = str_replace(" ", "_", $tags[$i]);
    $j = -1;
    $k = -1;
    $len = strlen($to_compare);
    while ( $k + 1 < $len && !(($to_compare[$k + 1] >= 'A' && $to_compare[$k + 1] <= 'Z')
    || ($to_compare[$k + 1] >= 'a' && $to_compare[$k + 1] <= 'z')
    || $to_compare[$k + 1] == ' ' || $to_compare[$k + 1] == ','))
    {
      $k++;
      $len--;
    }
    while (++$j < $len_del && ++$k < strlen($to_compare))
    if ($to_compare[$k] != $del_tag[$j])
    break ;
    if ($j == $len_del && $len_del != 0)
    unset($tags[$i]);
    else
    if (!strcmp($to_compare, $del_tag))
    unset($tags[$i]);
  }
  $tags = implode(", ", $tags);
  Bdd::alter_table($id, "tags", $tags, "users_profile");
}

/*
** RETOUR DES VALEURS DEJA SELCTIONNEES PAR LUTILISATEUR
*/
$gender = Bdd::get_user_field_id($id, "genre", "users_profile");
$orientation = Bdd::get_user_field_id($id, "orientation", "users_profile");
$biographie = Bdd::get_user_field_id($id, "biographie", "users_profile");
$tags = Bdd::get_user_field_id($id, "tags", "users_profile");
echo("<script>
var selects = document.getElementsByName('Genre');
for(var i = 0; i < selects[0].children.length; i++)
if(!selects[0].children[i].innerHTML.localeCompare('" . $gender . "'))
selects[0].children[i].setAttribute('selected', 'selected');
</script>");
echo("<script>
var selects = document.getElementsByName('Orientation');
for(var i = 0; i < selects[0].children.length; i++)
if(!selects[0].children[i].innerHTML.localeCompare('" . $orientation . "'))
selects[0].children[i].setAttribute('selected', 'selected');
</script>");
echo("<script>
var bio = document.getElementById('Biographie');
bio.style.whiteSpace ='normal';
bio.value= '" . str_replace("\r", '\\n\\', $biographie) . "';
</script>");
echo("<script>
var choose_file = document.getElementById('profilPict');
var preview = document.getElementById('preview');
console.log(choose_file);
choose_file.addEventListener('change', updateImageDisplay);
function updateImageDisplay() {
    while (preview.firstChild) {
      preview.removeChild(preview.firstChild);
    }
  
    var curFiles = choose_file.files;
    console.log(curFiles);
    if(curFiles.length === 0) {
      var para = document.createElement('p');
      para.textContent = 'Aucun fichier sélectionné pour le moment';
      preview.appendChild(para);
    }
    else {
        var para = document.createElement('p');
  
        para.textContent = 'Nom du fichier ' + curFiles[0].name + ', taille du fichier ' + returnFileSize(curFiles[0].size) + '.';
        preview.appendChild(para);  
      }
    }
    function returnFileSize(number) {
      if(number < 1024) {
        return number + ' octets';
      }
      else if(number >= 1024 && number < 1048576) {
        return (number/1024).toFixed(1) + ' Ko';
      }
      else if(number >= 1048576) {
        return (number/1048576).toFixed(1) + ' Mo';
      }
    }</script>");
$tags = preg_replace('/[\x00-\x1F\x7F-\xA0\xAD]/u', '', $tags);
$tags = explode(", ", $tags);
$tags_nb = count($tags);
$i = -1;
while (++$i < $tags_nb)
$tags[$i] = '<a href="index.php?p=del&tag=' . str_replace(" ", "_", $tags[$i]) . '"> #' . $tags[$i] . "</a>";
$tags = implode(", ", $tags);
if (strlen($tags) > 0)
$tags = "Vos tags : " . $tags;
echo("<script>
var tag = document.getElementById('tag');
tag.  innerHTML= '" . $tags . "'
</script>");
?>
