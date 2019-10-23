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
  $form->search_field("Localisation");
  $form->hidden_entry("geoloc", "geoloc");
  $form->entry("Tags", "text", "tags", null, "Séparez les tags par une virgule");
  $form->button("Rechercher !");
  echo("</div>");

  echo("<script>
// function updateIp(response, status)
// {
//   alert('updateIp');
//   var reponse = JSON.Parse(xmlhttp.response);
//   if (reponse[ip])
//   var ip = reponse[ip];
//   return ip;
// }

const locField = $('#locationField input');

locField.on('input', function(ev) {
  console.log('trigger')
  if (ev.target.value) {
    fetch('https://api.mapbox.com/geocoding/v5/mapbox.places/' + ev.target.value + '.json?access_token=pk.eyJ1IjoibWMxMDBzIiwiYSI6ImNqb2E2ZTF3ODBxa3czd2xldHp1Z2FxbGYifQ.U4oatm5RsTXXHQLz5w66dQ')
      .then(res => res.json())
      .then(res => {
        // console.log(res)
        if (res.features) {
          var countries = res.features.map(f => {return {name : f.place_name, lat: f.geometry.coordinates[1], long: f.geometry.coordinates[0]}})

          autocomplete(document.getElementById('myInput'), countries, this);
        }
      })
  }
  else
    autocomplete(document.getElementById('myInput'), [{name: 'Ma position'}], this);
})

locField.on('click', function(ev) {
  autocomplete(document.getElementById('myInput'), [{name: 'Ma position'}], this);
})

function autocomplete(inp, arr, h) {
  var currentFocus;
  console.log(arr);
  
      var a, b, i, val = h.value;
      
      closeAllLists();
      // if (!val) { return false;}
      currentFocus = -1;
      a = document.createElement('DIV');
      a.setAttribute('id', h.id + 'autocomplete-list');
      a.setAttribute('class', 'autocomplete-items');
      h.parentNode.appendChild(a);
      for (i = 0; i < arr.length; i++) {
        if (arr[i].name.substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          b = document.createElement('DIV');
          b.innerHTML = '<strong>' + arr[i].name.substr(0, val.length) + '</strong>';
          b.innerHTML += arr[i].name.substr(val.length);
          b.innerHTML += '<input type=\"hidden\" value=\"' + arr[i].name + '\">';
          b.addEventListener('click', function(e) {
              inp.value = this.getElementsByTagName('input')[0].value;
              find = arr.find(city => city.name == inp.value)
              document.getElementById('geoloc').value = find.lat + ':' + find.long;
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
        document.getElementById('geoloc').value = res.results[0].geometry.lat + ':' + res.results[0].geometry.lng;
        document.getElementById('myInput').value = 'Ma position - ' + component.house_number + ' ' + component.street + ', ' + component.postcode + ' '+ component.city + ', ' + component.country;
      })
  }

  function getGeoip(ip) {
    fetch('https://freegeoip.app/json/' + ip)
      .then(res => res.json())
      .then(res => {
        document.getElementById('geoloc').value = res.latitude + ':' + res.longitude;
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
}
else
{
  /*
  ** Treating formular datas
  */
  // print_r($_POST);
  // Array ( [age_min] => bkiy [age_max] => 1 [likes_min] => [likes_max] => [localisation] => [tags] => ) 
  $age_filter = '';
  $likes_filter = '';
  $location_filter = '';
  $tags_filter = '';

  if (isset($_POST['age_min']) && $_POST['age_min'] && isset($_POST['age_max']) && $_POST['age_max']) {
    $age_filter = ' AND `age` <= ' .$_POST['age_max']. ' AND `age` >= '.$_POST['age_min'];
  }
  if (isset($_POST['likes_min']) && $_POST['likes_min'] && isset($_POST['likes_max']) && $_POST['likes_max']) {
    $likes_filter = ' AND `likes_nb` <= ' .$_POST['likes_max']. ' AND `likes_nb` >= '.$_POST['likes_min'];
  }
  if (isset($_POST['location']) && $_POST['location']) {
    $geometry = explode(':', $_POST['geoloc']);
    $location_filter = ' ORDER BY ABS('.$geometry[0].' - latitude) ASC, ABS('.$geometry[1].' - longitude) ASC';
  }
  if (isset($_POST['tags']) && $_POST['tags']) {
    $tags_filter = ' AND INSTR( tags , "'.$_POST['tags'].'" )';
  }

  $query = 'SELECT `id` FROM matcha.`users` WHERE `id` != '.$_SESSION['id'].$age_filter.$likes_filter;
  $query_profils = 'SELECT * FROM matcha.users_profile WHERE `id` != '.$_SESSION['id'].$tags_filter.$location_filter;

  $filtered_ageandlikes_profils = Bdd::order_profils($query);
  $filtered_locationandtags_profils = Bdd::order_profils($query_profils);
  $filtered_profils = [];
  foreach ($filtered_locationandtags_profils as $key => $profil) {
    foreach ($filtered_ageandlikes_profils as $value) {
      if ($value['id'] == $profil['id']) 
        array_push($filtered_profils, $profil);
    }
  }
  

  if (!(isset($filtered_profils) && count($filtered_profils) > 0))
    $layout->main_error("Aucun utilisateur ne correspond a votre recherche");
  else {
    $nb_users = count($filtered_profils);
    $nb_pages = ceil($nb_users/10);
    if (isset($_GET['page']))
      $page = intval(htmlspecialchars($_GET['page']));
    else
      $page = 0;
    Members::display_profils_cards($filtered_profils);
    Members::display_pagination($page, $nb_pages, 'index.php?p=extended_search&page=');
  }
}

//   Form::get_extended_search_datas();
//   if (!isset($_GET['page']) || !isset($_SESSION['profiles']))
//     $profiles = Bdd::find_extended_search_profiles();
//   else if (isset($_SESSION["profiles"]))
//     $profiles = $_SESSION["profiles"];
//   Layout::debug("");
//   // print_r($profiles);

//   /*
//   ** Displaying users
//   */
//   /*
//   ** defining needed functions
//   */
//   function get_users_with_idx($users, $idx_min, $pic_per_page)
//   {
//     $result = array();
//     $go = 0;
//     $id_min = $users[$idx_min]['id'];
//     foreach ($users as $user)
//     {
//       if ($go <= $pic_per_page && ($user['id'] == $id_min || $go > 0))
//       {
//         ++$go;
//         array_push($result, $user);
//       }
//     }
//     return ($result);
//   }

//   function get_users_info($users, $id_min, $nb_users)
//   {
//     $users_info = Bdd::get_field_with_conditions("users", "*", "id >= " . $id_min);
//     $i = -1;
//     $result = array();
//     while (isset($users[++$i]) && $i < $nb_users)
//       array_push($result, $users_info[$i]);
//     return ($result);
//   }

//   function get_users_profile($users, $id_min, $nb_users)
//   {
//     $users_info = Bdd::get_field_with_conditions("users_profile", "*", "id >= " . $id_min);
//     $i = -1;
//     $result = array();
//     while (isset($users[++$i]) && $i < $nb_users)
//       array_push($result, $users_info[$i]);
//     return ($result);
//   }

//   /*
//   ** defining loop variables
//   */
//   $pic_per_page = 6;
//   $nb_users = count($profiles);
//   $nb_pages = ceil($nb_users / $pic_per_page);
//   if (isset($_GET['page']))
//     $page = intval(htmlspecialchars($_GET['page']));
//   else
//     $page = 0;
//   /*
//   ** getting users with appropriate id
//   */
//   $idx_min = $pic_per_page * $page;
//   if ($idx_min > $nb_users)
//     $idx_min = 0;
//   $i = -1;
//   if (!(isset($profiles) && count($profiles) > 0))
//     $layout->main_error("Aucun utilisateur ne correspond a votre recherche");
//   else
//     $_SESSION["profiles"] = $profiles;
//   $to_display = get_users_with_idx($profiles, $idx_min, $pic_per_page);
//   $users_profile = get_users_profile($profiles, $profiles[$idx_min]['id'], count($to_display));
//   $users_info = get_users_info($profiles, $profiles[$idx_min]['id'], count($to_display));
//   Members::display_user($users_profile, $users_info, 0, "extended");
//   Members::display_pagination($page, $nb_pages, "index.php?p=extended_search&page=");
// }
 ?>