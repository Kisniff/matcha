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
$form->search_field("Localisation");
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

const locField = $('#locationField input');

locField.on('input', function(ev) {
  console.log('trigger')
  if (ev.target.value) {
    fetch('https://api.mapbox.com/geocoding/v5/mapbox.places/' + ev.target.value + '.json?access_token=pk.eyJ1IjoibWMxMDBzIiwiYSI6ImNqb2E2ZTF3ODBxa3czd2xldHp1Z2FxbGYifQ.U4oatm5RsTXXHQLz5w66dQ')
      .then(res => res.json())
      .then(res => {
        if (res.features) {
          var countries = res.features.map(f => f.place_name)

          autocomplete(document.getElementById('myInput'), countries, this);
        }
      })
  }
  else
    autocomplete(document.getElementById('myInput'), ['Ma position'], this);
})

locField.on('click', function(ev) {
  console.log('click')
  autocomplete(document.getElementById('myInput'), ['Ma position'], this);
})

function autocomplete(inp, arr, h) {
  console.log(arr, inp);
  var currentFocus;
  
      var a, b, i, val = h.value;
      console.log(val)
      
      closeAllLists();
      // if (!val) { return false;}
      currentFocus = -1;
      a = document.createElement('DIV');
      a.setAttribute('id', h.id + 'autocomplete-list');
      a.setAttribute('class', 'autocomplete-items');
      h.parentNode.appendChild(a);
      for (i = 0; i < arr.length; i++) {
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          b = document.createElement('DIV');
          b.innerHTML = '<strong>' + arr[i].substr(0, val.length) + '</strong>';
          b.innerHTML += arr[i].substr(val.length);
          b.innerHTML += '<input type=\"hidden\" value=\"' + arr[i] + '\">';
          b.addEventListener('click', function(e) {
              inp.value = this.getElementsByTagName('input')[0].value;
              closeAllLists();
          });
          a.appendChild(b);
        }
      }

  inp.addEventListener('keydown', function(e) {
      var x = document.getElementById(this.id + 'autocomplete-list');
      if (x) x = x.getElementsByTagName('div');
      if (e.keyCode == 40) {
        currentFocus++;
        addActive(x);
      } else if (e.keyCode == 38) { //up
        currentFocus--;
        addActive(x);
      } else if (e.keyCode == 13) {
        e.preventDefault();
        if (currentFocus > -1) {
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    if (!x) return false;
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    x[currentFocus].classList.add('autocomplete-active');
  }
  function removeActive(x) {
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove('autocomplete-active');
    }
  }
  function closeAllLists(elmnt) {
    var x = document.getElementsByClassName('autocomplete-items');
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  function getPos(pos)
  {
    fetch('https://api.opencagedata.com/geocode/v1/json?q=' + pos.coords.latitude + '+' + pos.coords.longitude + '&key=a1674daf25f54056a7c8047ca1742c22&no_annotations=1&language=fr')
      .then(res => res.json())
      .then(res => {
        let component = res.results[0].components;
        document.getElementById('myInput').value = 'Ma position - ' + component.house_number + ' ' + component.street + ', ' + component.postcode + ' '+ component.city + ' - ' + component.country;
      })
  }

  function getGeoip(ip) {
    fetch('https://freegeoip.app/json/' + ip)
      .then(res => res.json())
      .then(res => {
        document.getElementById('myInput').value = 'Ma position - ' + res.zip_code + ' ' + res.city + ', ' + res.country_name;
      })
  }

  function getIp(pos) {
    fetch('https://api.ipify.org/?format=json')
      .then(res => res.json())
      .then(res => {
        getGeoip(res.ip);
      })
  }

  document.addEventListener('click', function (e) {
    if (e.target.textContent == 'Ma position') {
      var localisation = navigator.geolocation.getCurrentPosition(getPos, getIp);
    }
    closeAllLists(e.target);
  });
}
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
  echo("<div class='row col-sm-12 pict-profil'>");
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
// console.log(choose_file);
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
        var filename = document.createElement('p');
  
        filename.textContent = 'Nom du fichier : ' + curFiles[0].name;
        preview.appendChild(filename); 

        var filesize = document.createElement('p');
  
        filesize.textContent = 'Taille du fichier : ' + returnFileSize(curFiles[0].size);
        preview.appendChild(filesize); 
        
        if (curFiles[0].size > 100000) {
          var errMes = document.createElement('p');

          errMes.setAttribute('class', 'error_message')
          errMes.textContent = 'La taille de votre image excède 97 Ko !';
          preview.appendChild(errMes);
        }
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
