<?php
Class Bdd{

  public $db;

  public function __construct($dsn="mysql:host=localhost", $login = "root", $pwd="mthiery")
  {
    if ($this->db = new PDO($dsn, $login, $pwd))
  {
    // $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return ($this->db);
  }
    // try
    // {
    //   $this->db = new PDO($dsn, $login, $pwd);
    // }
    // catch (Exception $d){
    //     $this->db = new PDO($dsn, $login, $pwd);
    // }
    // $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // return ($this->db);
  }

  public function init_bdd($db_name="matcha", $table_name="users"){
    $this->drop_db();
    $this->create_db();
    $query = "CREATE TABLE matcha.users(
      id INT AUTO_INCREMENT PRIMARY KEY,
      login VARCHAR(256) NOT NULL,
      first_name VARCHAR(256) NOT NULL,
      last_name VARCHAR(256) NOT NULL,
      email VARCHAR(256) NOT NULL,
      confirmed boolean NOT NULL default 0,
      location VARCHAR(256),
      password VARCHAR(256) NOT NULL,
      age INT,
      likes LONGTEXT DEFAULT NULL,
      matches LONGTEXT DEFAULT NULL,
      likes_nb INT default 0,
      id_liked LONGTEXT DEFAULT NULL,
      reported_by_id LONGTEXT DEFAULT NULL,
      blocked_id LONGTEXT default NULL)";
    $this->query($query);
    $query = "CREATE TABLE matcha.users_profile(
      id INT AUTO_INCREMENT PRIMARY KEY,
      genre VARCHAR(256) DEFAULT 'non-binaire',
      orientation VARCHAR(256) DEFAULT 'bisexuel.le',
      biographie LONGTEXT DEFAULT null,
      images LONGTEXT,
      tags LONGTEXT)";
    $this->query($query);
    $query = "CREATE TABLE matcha.matched_conv(
      id INT AUTO_INCREMENT PRIMARY KEY,
      id_member_a INT default 0,
      id_member_b INT default 0,
      messages LONGTEXT DEFAULT null)";
    $this->query($query);
    $query = "CREATE TABLE matcha.notifications(
      id INT AUTO_INCREMENT PRIMARY KEY,
      id_member_a INT default 0,
      id_member_b INT default 0,
      is_new BOOL default 1,
      notif LONGTEXT DEFAULT null)";
    $this->query($query);
    $this->add_user("root@root.com", "root", "root", "root", "root", "users", 1);
  }

  public function query($statement)
  {
    $instruct = $this->db->prepare($statement);
    $instruct->execute();
    return ($instruct);
  }

  public static function user_exist($mail, $login=null, $table="users", $db_name = "matcha") {

    if (isset($login))
    {
      $instruct = (new self)->query("SELECT login FROM " . $db_name . "." . $table);
      $datas = $instruct->fetchAll();
      foreach($datas as $db_login)
        foreach($db_login as $b_login)
          if ($b_login == $login)
            return ("login");
    }
    if (isset($mail))
    {
      $instruct = (new self)->query("SELECT email FROM " . $db_name . "." . $table);
      $datas = $instruct->fetchAll();
      foreach($datas as $db_mail)
        foreach($db_mail as $b_mail)
          if ($b_mail == $mail)
            return ("mail");
    }
    return (false);
  }

  public function are_logins_incorrect($mail, $password, $table = "users", $db_name = "matcha")
  {
      if ($this->user_exist($mail) != "mail")
        return ("mail");
      $instruct = $this->db->query("SELECT `password`, `confirmed`, `key` FROM " . $db_name . "." . $table . " WHERE email = '" . $mail . "'");
      $datas = $instruct->fetchAll();
      if ($datas[0]['confirmed'] == 0)
        return ("unconfirm");
      if ($password == "I forgot my fucking password!!")
      {
        $key = $datas[0]['key'];
        $this->send_mail($mail, 'Réinitialisez votre mot de passe.', 'resetpwd&key='.$key, "Réinitialisation de mot de passe");
      }
      else if (!password_verify(htmlspecialchars($password), $datas[0]['password']))
        return ("password");
      return (false);
  }

  public function reset_pwd($mail, $password, $key = '1', $table = "users", $db_name = "matcha")
  {
      if ($this->user_exist($mail) != "mail")
        return ("mail");
      $instruct = $this->db->query("SELECT `confirmed`, `key` FROM " . $db_name . "." . $table . " WHERE email = '" . $mail . "'");
      $datas = $instruct->fetchAll();
      if ($datas[0]['confirmed'] == 0)
        return ("unconfirm");
      if ($datas[0]['key'] != $key)
        return ("invalid key");
      $user_id = $this->get_user_field($mail, "id");
      $pwd = password_hash(htmlspecialchars($password), PASSWORD_DEFAULT);
      if (Form::password_checker(htmlspecialchars($password), true))
        $this->alter_table($user_id, "password", $pwd);
      else
        return ("password");
      return (false);
  }

  public function prepare($query){
    return ($this->db->prepare($query));
  }

  public static function get_user_field($email, $field, $table = "users", $db_name = "matcha"){
	  $instruct = (new self)->query("SELECT " . $field . " FROM " . $db_name . "." . $table . " WHERE email = '" . $email . "'");
	  $data = $instruct->fetch();
	  return($data[0]);
  }

  public static function get_user_field_id($id, $field, $table = "users", $db_name = "matcha"){
	  $instruct = (new self)->query("SELECT " . $field . " FROM " . $db_name . "." . $table . " WHERE id = '" . $id. "'");
	  $data = $instruct->fetch();
	  return($data[0]);
  }

  private function drop_db($db_name = "matcha"){
    $query = "DROP DATABASE IF EXISTS " . $db_name . ";";
    $this->query($query);
  }

  private function create_db($db_name="matcha"){
    $query = "CREATE DATABASE " . $db_name . ";";
    $this->query($query);
  }

  public static function alter_table($id, $column, $value, $table = "users", $arr=false, $db = "matcha")
  {
    if ($arr == true)
    {
      $statement = "UPDATE " . $db . "." . $table . " SET " . $column . " = :value WHERE id = " . $id;
      $instruct = (new self)->db->prepare($statement);
      $value = base64_encode(serialize($value));
      $instruct->bindParam(':value', $value, PDO::PARAM_STR);
      $instruct->execute();
    }
    else if ($column == 'confirmed')
    {
      $statement = "UPDATE " . $db . "." . $table . " SET " . $column . " = 1 WHERE id = " . $id . " AND `key` = :value";
      $instruct = (new self)->db->prepare($statement);
      $instruct->bindParam(':value', $value, PDO::PARAM_STR);
      $instruct->execute();
    }
    else if ($column != "images")
    {
      $value = str_replace("'", "", $value);
      $instruct = "UPDATE " . $db . "." . $table . " SET " . $column . " = '" . $value . "' WHERE id = " . $id;
      $instruct = (new self)->query($instruct);
    }
    else
    {
      $statement = "UPDATE " . $db . "." . $table . " SET " . $column . " = :value WHERE id = " . $id;
      $instruct = (new self)->db->prepare($statement);
      $instruct->bindParam(':value', $value, PDO::PARAM_STR);
      $instruct->execute();
    }
  }

  public static function add_picture($url, $user_id, $table ="users_profile")
  {
    $images = self::get_user_field_id($user_id, "images", "users_profile");
    $images = unserialize($images);
    if ($images != null)
      array_push($images, $url);
    else
      $images = array($url);
    self::alter_table($user_id, "images", serialize($images), "users_profile");
    return ;
  }

  public static function del_picture($user_id, $idx)
  {
    $images = self::get_user_field_id($user_id, "images", "users_profile");
    $images = unserialize($images);
    $result = array();
    $i = -1;
    $count = (empty($images) || !is_array($images)) ? 1 : count($images);
    while (++$i < $count)
      if ($i != intval($idx))
        array_push($result, $images[$i]);
    if (count($result) == 0)
      self::alter_table($user_id, "images", null, "users_profile");
    else
      self::alter_table($user_id, "images", serialize($result), "users_profile");
  }

  public static function get_field_with_conditions($table, $field, $condition, $order = "ASC", $db = "matcha")
  {
    $bdd = new Bdd;
    $query = "SELECT " . $field . " FROM " . $db . "." . $table . " WHERE (" . $condition . ") ORDER BY id " . $order;
    return ($bdd->query($query)->fetchAll());
  }

  public static function count_field($field, $table, $condition = null, $db = 'matcha')
  {
    $bdd = new Bdd;
    if ($condition == null)
      $query = "SELECT COUNT(" . $field . ") FROM " . $db . "." . $table;
    else
      $query = "SELECT COUNT(" . $field . ") FROM " . $db . "." . $table . " WHERE " . $condition;
    return ($bdd->query($query)->fetch()[0]);
  }

  public static function is_id_valid($id, $table, $database = "matcha")
  {
    $bdd = new Bdd();
    $instruct = $bdd->query("SELECT * FROM " . $database . "." . $table . " WHERE id=" . $id);
    return($instruct->fetch());
  }

  public static function find_extended_search_profiles()
  {
      $bdd = new bdd();
      $query = "SELECT login, id FROM matcha.users WHERE "
      . "likes_nb BETWEEN '" . $_SESSION['likes_min'] . "' AND '" . $_SESSION['likes_max'] . "'"
      . " AND age BETWEEN '" . $_SESSION['age_min'] . "' AND '" . $_SESSION['age_max']  . "'"
      ;
      $instruct = $bdd->query($query);
      return($instruct->fetchAll());
  }

  public function add_user($mail, $login, $first_name, $last_name, $mdp=null, $table="users", $bool=0)
  {
    if (($ret = $this->user_exist($mail, $login, $table)))
    {
      if ($ret == "mail")
        return (Form::field_error("Email", "Le login renseigné est déjà utilisé"));
      if ($ret == "login")
        return (Form::field_error("Login", "Le login renseigné est déjà utilisé"));
    }
    // create key
    $key = "";
    for($i=1 ; $i<15 ; $i++)
    $key .= mt_rand(0,9);

    $mdp = password_hash(htmlspecialchars($mdp), PASSWORD_DEFAULT);
    $query = "INSERT INTO matcha." . $table . " VALUES (id, :login, :first_name, :last_name, :mail, :bool, :key, location, :pwd, age, likes, matches, likes_nb, id_liked, reported_by_id, blocked_id)";
    $instruct = $this->db->prepare($query);
    $instruct->bindParam(':login', $login, PDO::PARAM_STR);
    $instruct->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $instruct->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $instruct->bindParam(':mail', $mail, PDO::PARAM_STR);
    $instruct->bindParam(':bool', $bool, PDO::PARAM_STR);
    $instruct->bindParam(':key', $key, PDO::PARAM_STR);
    $instruct->bindParam(':pwd', $mdp, PDO::PARAM_STR);
    $instruct->execute();
    $query = "INSERT INTO matcha.users_profile VALUES(id, genre, orientation, null, null, null);";
    $instruct = $this->query($query);

    // add send mail for confirm
    $this->send_mail($mail, 'Confirmez votre compte !', 'Connexion&key='.$key, "Confirmation de compte");

    return (true);
  }

  public static function send_mail($mail, $action, $page, $object)
  {
    // add send mail for confirm
    $header="MIME-Version: 1.0\r\n";
    $header.='From: Matcha.com <support@matcha.com>'."\n";
    $header.='Content-Type:text/html; charset="uft-8"'."\n";
    $message='
    <html>
      <body>
        <div align="center">
          <a href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?p='.$page.'">'.$action.'</a>
        </div>
      </body>
    </html>
    ';
    if ($mail = mail($mail, $object, $message, $header))
      print('Un email vous a été envoyé ! </br> Veuillez vérifier votre boîte de réception pour confirmer votre email.');
    else
      print("L'envoi de l'email de confirmation à échoué ! </br> Veuillez vérifier si votre adresse mail est valide et rééssayez.");
  }

  public static function add_notif($id_member_a, $id_member_b, $notif, $database = "matcha")
  {
    $db = new Bdd();
    $query = "INSERT INTO " . $database . ".notifications VALUES(id, :id_member_a, :id_member_b, is_new, :notif)";
    $instruct = $db->prepare($query);
    $instruct->bindParam(':id_member_a', $id_member_a, PDO::PARAM_INT);
    $instruct->bindParam(':id_member_b', $id_member_b, PDO::PARAM_INT);
    $instruct->bindParam(':notif', $notif, PDO::PARAM_STR);
    $instruct->execute();
  }

  public static function del_notif($id, $database = "matcha")
  {
    $bdd = new Bdd();
    $query = "DELETE FROM " . $database . ".notifications WHERE (id = " . $id . ")";
    $bdd->query($query);
  }
}
