<?php
include_once("Layout.php");
class Form
{
  private $layout;

  public function __construct($method = null, $action = null)
  {
    $this->layout = new Layout();
    if (!isset($method))
      return ;
    echo("
    <form method='" . $method . "' action='" . $action . "'>
    <div class='container'>"
  );
  }

  public static function file($name, $action)
  {
    // echo('
    // <form method="POST" action="' . $action . '" enctype="multipart/form-data">
    //  <input type="hidden" name="MAX_FILE_SIZE" value="100000">
    //  Fichier : <input type="file" name="' . $name . '" accept=".jpg, .jpeg, .png">
    //  <input type="submit" name="envoyer" value="Uploader !">
    // </form>
    // ');
    echo('
    <form method="POST" action="' . $action . '" enctype="multipart/form-data" style="text-align: center;">
     <input type="hidden" name="MAX_FILE_SIZE" value="100000">
						<label for="profilPict" class="label-file">Choisir une image</label>
            <input class="input-file" type="file" id="profilPict" name="' . $name . '" class="upl_file" accept=".jpg, .jpeg, .png"><br/>
            <div id="preview">
					<p>Aucun fichier sélectionné pour le moment</p>
				</div>
            <input type="submit" name="envoyer" value="Uploader !">
            </form>
    ');

  }

  public static function download_file($name, $id)
  {
    $images = Bdd::get_user_field_id($id, "images", "users_profile");
    $images = unserialize($images);
    if (!empty($images) && count($images) >= 5)
        echo '<p class="error_message">Vous ne pouvez pas upload plus de 5 images</p>';
    else
    {
       $dossier = 'tmp_pics/';
       $fichier = basename($_FILES['photo']['name']);
       $error_upload = "";
       if ($_FILES['photo']['error'] == 2)
        $error_upload = ", la taille de votre image excède 97 Ko";
      else if ($_FILES['photo']['error'] == 4)
        $error_upload = ", vous n'avez pas sélectionné d'image";
      //  print_r($_FILES);
       if(move_uploaded_file($_FILES['photo']['tmp_name'], $dossier . $fichier))
       {
          if (mime_content_type($dossier . $fichier) != "image/png"
          && mime_content_type($dossier . $fichier) != "image/jpeg")
          {
            echo('<p class="error_message">Veuillez uploader un fichier png ou jpg</p>');
            return ;
          }
          $im = base64_encode(file_get_contents($dossier . $fichier));
          $src = 'data: '.mime_content_type($dossier . $fichier).';base64,'.$im;
          Bdd::add_picture($src, $id);
        }
        else if ($_FILES['photo']['error'] != 0)
          echo '<p class="error_message" style="text-align: center; padding-top: 10px">Echec de l\'upload' .$error_upload. ' !</p>';
    }
  }

  public function hidden_entry($name, $id)
  {
    echo("
    <input type='hidden' name='" . $name . "' id='" . $id ."'>
    ");

  }

  public static function interval($label, $name, $value_a = null, $value_b = null)
  {
    echo("
    <div class='row'>
      <div class='col-sm-1'></div>
      <label class='col-sm-4'>" . $label . "</label>
      <div class='col-sm-4 row'>
        <input class='col-sm-2' type='text' name='" . $name . "_min'>
        <div class='col-sm-1'></div>
        <input class='col-sm-2' type='text' name='" . $name . "_max'>
      </div>
    </div>");
  }

  public function entry($label, $input_type, $name, $value = null, $sub_title = null)
  {
    echo("
      <div class='row'>
        <div class='col-sm-1'>");
    echo ("</div>
        <label class='col-sm-4'>" . $label);
    if ($sub_title != null)
    {
      echo("
      <p class='sub-title'>" . $sub_title . "</p>
      ");
    }
    echo("</label>");
    if ($value == null)
      echo("
          <input id='" . $label . "' class='col-sm-7' type='" . $input_type . "' name='" . $name . "'/>
        </div>
      ");
    else
    {
      echo("
          <input id='" . $label . "' class='col-sm-7' type='" . $input_type . "' name='" . $name . "' value='" . $value . "'/>
        </div>
      ");
    }
    $this->layout->white_space(1);
  }

  public function button($label)
  {
    echo("
        <div class='row'>
          <div class='col-sm-9'></div>
          <input class='col-sm-3' type='submit' value='" . $label ."'/>
        </div>
      </form>
      </div>
    ");
  }

  public function password_checker($password, $oldpwd = false)
  {
    $field = $oldpwd ? "Nouveau mot de passe" : "Mot de passe";
    if (strlen($password) < 8)
    {
      return (self::field_error($field, "Votre mot de passe doit contenir au moins 8 caractères"));
    }
    if (preg_match("/[a-z]/s", $password) !== 0 && preg_match("/[A-Z]/s", $password) !== 0
    && preg_match("/[0-9]/s", $password) && preg_match("/([\!-\/]|[:-@])/s", $password) !== 0)
      return (true);
    else
      return (self::field_error($field, "Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial"));
  }

  public function first_name_checker($name)
  {
    if (isset($_SESSION['first_name']) && strlen($_SESSION['first_name']) > 0)
      return (true);
    return (self::field_error("Prenom", "Veuillez indiquer votre prénom"));
  }

  public function last_name_checker($name)
  {
    if (isset($_SESSION['last_name']) && strlen($_SESSION['last_name']) > 0)
      return (true);
    return (self::field_error("Nom", "Veuillez indiquer votre nom de famille"));
  }

  public function email_checker($email)
  {
    if (isset($_SESSION['email']) && strlen($_SESSION['email']) > 0)
      return (true);
    return (self::field_error("Email", "Veuillez indiquer votre nom email"));
  }

  public function login_checker($login)
  {
    if (isset($_SESSION['login']) && strlen($_SESSION['email']) > 0)
      return (true);
    return (self::field_error("Login", "Veuillez indiquer votre login"));
  }

  public function subscription_validity()
  {
    if ($this->login_checker($_SESSION['login'])
    && $this->last_name_checker($_SESSION['last_name'])
    && $this->first_name_checker($_SESSION['first_name'])
    && $this->email_checker($_SESSION['email'])
    && $this->password_checker($_SESSION['pwd']))
      return (true);
    return (false);
  }

  public function are_all_subscription_fields_set()
  {
    if (isset($_POST['login']) && isset($_POST['last_name'])
    && isset($_POST['first_name']) && isset($_POST['email'])
    && isset($_POST['pwd']) && $_POST['login'] != ""
    && $_POST['last_name'] != "" && $_POST['first_name'] != ""
    && $_POST['email'] != "" && $_POST['pwd'] != "")
        return (true);
    return (false);
  }

  public function are_all_google_subscription_fields_set()
  {
    if (isset($_POST['login']) && isset($_POST['last_name'])
    && isset($_POST['first_name']) && isset($_POST['pwd'])
    && $_POST['login'] != "" && $_POST['last_name'] != ""
    && $_POST['first_name'] != "" && $_POST['pwd'] != "")
        return (true);
    return (false);
  }

  public function subscr_varpost_to_varsession()
  {
    $_SESSION['login'] = htmlspecialchars($_POST['login']);
    if (isset($_POST['email']))
      $_SESSION['email'] = htmlspecialchars($_POST['email']);
    $_SESSION['last_name'] = htmlspecialchars($_POST['last_name']);
    $_SESSION['pwd'] = htmlspecialchars($_POST['pwd']);
    $_SESSION['first_name'] = htmlspecialchars($_POST['first_name']);
  }

  public function validation_message($message)
  {
    $this->layout->white_space(1);
    echo("<div class='col-sm-12 row'>");
    echo("<p class='validation_message col-sm-12 text-center'>" . $message . "</p>");
    echo("</div>");
  }

  public static function are_connexion_fields_ok()
  {
    if (!isset($_POST['email']))
      return (self::field_error("Email", "Veuillez renseigner un email"));
    $_SESSION['email'] = htmlspecialchars($_POST['email']);
    if (!isset($_POST['pwd']))
      return (self::field_error("Mot de passe", "Veuillez renseigner un mot de passe"));
    if (strlen($_POST['email']) > 0)
      if (strlen($_POST['pwd']) > 0)
        return (true);
  }

  public static function email_field_ok()
  {
    if (!isset($_POST['email']))
      return (self::field_error("Email", "Veuillez renseigner un email"));
    $_SESSION['email'] = htmlspecialchars($_POST['email']);
    if (strlen($_POST['email']) > 0)
        return (true);
  }

  public static function field_error($id, $error_message)
  {
    echo("
    <script>
      var field = document.getElementById('" . $id . "');
      field.setAttribute('class', 'error_field col-sm-7');
      var inputs = document.getElementsByTagName('label');
      for (var i = 0; i < inputs.length; i++){
        var input = inputs[i].textContent;
        if (input == '" . $id . "')
        {
          inputs[i].setAttribute('class', 'col-sm-4 error_field');
        }
      }
    </script>
    ");
    (new self)->layout->white_space(1);
    echo(
      "<div class='col-sm-12 row'>
        <p class='col-sm-12 error_message text-center'>" . $error_message . "</p>
      </div>
      ");
    if ($id == "Mot de passe")
    {
      echo(
        "<div class='col-sm-12 row'>
          <p class='col-sm-12 error_message text-center'><a href='index.php?p=forgotpwd' id='fpwd-link'>Mot de passe oublié</a></p>
        </div>
        ");
    }
    return (false);
  }

  public function select($name, $options)
  {
    echo("<div class='row'>
      <div class='col-sm-1'></div>
      <label class='col-sm-4'>" . $name . "</label>");
    echo("<select class='col-sm-7' name='" . $name . "' size='1'>");
    for($i = 0; $i < sizeof($options); $i++)
    {
      echo("<option>" . $options[$i]);
    }
    echo("</select></div>");
    $this->layout->white_space(1);

  }

  public function search_field($name)
  {
    echo("<div class='row'>
      <div class='col-sm-1'></div>
      <label class='col-sm-4'>" . $name . "</label>");
    echo('<div class="autocomplete col-sm-7" id="locationField">
        <input id="myInput" style="width: 100%; padding: 0px 10px 0px 10px;" type="text" name="location" placeholder="Ville" autocomplete="off">
      </div></div>');
  $this->layout->white_space(1);
  }

  public function textarea($name, $content, $rows = 10)
  {
    echo("<div class='row'>
      <div class='col-sm-1'></div>
      <label class='col-sm-4'>" . $name . "</label>");
    echo("<textarea class='col-sm-7' rows='" . $rows . "' id='" . $name . "' name=" . $name . " placeholder=". $content ."
    ></textarea></div>");
    $this->layout->white_space(1);
  }

  public static function modif_user_vars()
  {
    $user_id = Bdd::get_user_field($_SESSION['email'], "id");
    if (isset($_POST['first_name']) && strlen(htmlspecialchars($_POST['first_name'])) > 0)
      Bdd::alter_table($user_id, "first_name", htmlspecialchars($_POST['first_name']));
    if (isset($_POST['last_name']) && strlen(htmlspecialchars($_POST['last_name'])) > 0)
      Bdd::alter_table($user_id, "last_name", htmlspecialchars($_POST['last_name']));
    if (isset($_POST['email']) && strlen(htmlspecialchars($_POST['email'])) > 0)
    {
      if (!Bdd::user_exist(htmlspecialchars($_POST['email'])))
      {
        Bdd::alter_table($user_id, "email", htmlspecialchars($_POST['email']));
        $_SESSION['email'] = htmlspecialchars($_POST['email']);
      }
      else
        if ($_SESSION['email'] != htmlspecialchars($_POST['email']))
          self::field_error("Email", "Cet email est deja utilisé");
    }
    if (isset($_POST['pwd']) && strlen(htmlspecialchars($_POST['pwd'])) > 0)
    {
      if ((new self)->password_checker(htmlspecialchars($_POST['pwd'])))
      {
        Bdd::alter_table($user_id, "password", password_hash(htmlspecialchars($_POST['pwd']), PASSWORD_DEFAULT));
      }
    }
    if (isset($_POST['location']))
    {
      $location_post = explode(", ", explode(" - ", htmlspecialchars($_POST['location']))[1]);
      if (count($location_post == 3))
        $location_aray = array(
        "postcode" => $location_post[0],
        "city" => $location_post[1],
        "country" => $location_post[2]
        );
      Bdd::alter_table($user_id, "location", serialize($location_aray));
    }
    else
    {//code de geolocalisation via IP
      $ip = $_SERVER['REMOTE_ADDR'];
      //localiser ip
    }
  }

  private function clean_tag_cloud($tags)
  {
      $i = -1;
      $len = strlen($tags);
      if ($len == 0)
        return (null);
      while (++$i < $len)
      {
        if (($tags[$i] >= 'A' && $tags[$i] <= 'Z')
         || ($tags[$i] >= 'a' && $tags[$i] <= 'z')
         || $tags[$i] == ' ' || $tags[$i] == ',')
          continue ;
        else
          $tags[$i] = '';
      }
      $i = -1;
      while (++$i < $len)
      {
        if ($tags[$i] != ' ' && $tags[$i] != ',')
          break ;
      }
      if ($i == $len)
        return (null);
      $i = -1;
      while(++$i < $len)
      {
        if ($tags[$i] != ',' && $tags[$i] != ' ')
          break ;
        $tags[$i] = '';
      }
      while(--$len > 0)
      {
        if ($tags[$len] != ',' && $tags[$len] != ' ')
          return ($tags);
        $tags[$len] = '';
      }
    return ($tags);
  }

  public static function modif_profile_vars($user_id)
  {
    // print_r($_POST);
    if (isset($_POST['Genre']))
    {
      $genre = htmlspecialchars($_POST['Genre']);
      if (strlen($genre) > 0)
        Bdd::alter_table($user_id, "genre", $genre, "users_profile");
    }
    if (isset($_POST['Orientation']))
    {
      $orientation = htmlspecialchars($_POST['Orientation']);
      if (strlen($orientation) > 0)
        Bdd::alter_table($user_id, "orientation", $orientation, "users_profile");
    }
    if (isset($_POST['Biographie']))
    {
      $biographie = htmlspecialchars($_POST['Biographie']);
      if (strlen($biographie) > 0)
      {
        Bdd::alter_table($user_id, "biographie", $biographie, "users_profile");
      }
    }
    if (isset($_POST['tags']))
    {
      $tags = htmlspecialchars($_POST['tags']);
      $previous_tags = Bdd::get_user_field_id($user_id, "tags", "users_profile");
      $tag_tab = explode(',', $tags);
      $tag_nb = count($tag_tab);
      $i = -1;
      while (++$i < $tag_nb)
      {
       $tag_tab[$i] = (new self)->clean_tag_cloud($tag_tab[$i]);
       if ($tag_tab[$i] == null)
          unset($tag_tab[$i]);
      }
      $tags = implode(', ', $tag_tab);
      if ($previous_tags != null && $tags != null)
        $new_tags = $previous_tags . ", " . $tags;
      else
        $new_tags = $tags;
      if (strlen($tags) > 0)
        Bdd::alter_table($user_id, "tags", $new_tags, "users_profile");
    }
    if (isset($_POST['location']) && $_POST['location'])
    {
      $location_post = explode(", ", explode(" - ", htmlspecialchars($_POST['location']))[1]);
      if (count($location_post) == 3)
        $location_array = array(
        "adress" => $location_post[0],
        "postcode" => explode(" ", $location_post[1])[0],
        "city" => explode(" ", $location_post[1])[1],
        "country" => $location_post[2]
        );
      else if (count($location_post) == 2)
        $location_array = array(
          "postcode" => explode(" ", $location_post[0])[0],
          "city" => explode(" ", $location_post[0])[1],
          "country" => $location_post[1]
          );
      if (isset($location_array))
        Bdd::alter_table($user_id, "location", serialize($location_array));
    }
  }

  public static function get_extended_search_datas()
  {
    if (isset($_POST['age_min']))
      $_SESSION['age_min'] = intval(htmlspecialchars($_POST['age_min']));
    else
      $_SESSION['age_min'] = 0;
    if (isset($_POST['age_max']))
      $_SESSION['age_max'] = intval(htmlspecialchars($_POST['age_max']));
    if (!isset($_SESSION['age_max']) || $_SESSION['age_max'] == 0)
      $_SESSION['age_max'] = 100;
    if (isset($_POST['likes_max']))
      $_SESSION['likes_max'] = intval(htmlspecialchars($_POST['likes_max']));
    if (!isset($_SESSION['likes_max']) || $_SESSION['likes_max'] == 0)
      $_SESSION['likes_max'] = 1000;
    if (isset($_POST['likes_min']))
      $_SESSION['likes_min'] = intval(htmlspecialchars($_POST['likes_min']));
    else
      $_SESSION['likes_min'] = 0;
    /*
    ** localisation et tags a traiter
    */
    // if (isset($_POST['localisation']))
    //   $_SESSION['localisation'] = htmlspecialchars($_POST['localisation']);
    // if (isset($_POST['tags']))
    //   $_SESSION['tags'] = htmlspecialchars($_POST['tags']);
    // if ($_SESSION['age_min'] > $_SESSION['age_max'])

    /*
    ** POUR LE DEBUG
    */
    // Layout::debug($_SESSION['age_min']);
    // Layout::debug($_SESSION['age_max']);
    // Layout::debug($_SESSION['likes_min']);
    // Layout::debug($_SESSION['likes_max']);
  }

}
?>
