<?php
Class Curl{

  public $curl;

  function __construct($url, $mode = "GET")
  {
    $this->curl = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false
    );
    curl_setopt_array($this->curl, $options);
    if ($mode == "POST")
      curl_setopt($this->curl, CURLOPT_POST, true);
  }

  function __destruct()
  {
    curl_close($this->curl);
  }

  public function set_header($header = null)
  {
    if ($header === null)
      return ;
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
  }

  public function exec($postfields = null)
  {
    if ($postfields === null)
    {
      return (curl_exec($this->curl));
    }
    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postfields);
    return (curl_exec($this->curl));
  }

  public function close()
  {

  }
}
?>
