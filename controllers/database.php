<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

class Database{

  private $conn;

  public function getConnection(){
    
    $this->conn = null;
                            // IGNORE THESE ERRORS, CODE WORKS ON SERVER, INTELEPHENSE IS CONFUSED
    try{
      $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=zadanie4", DB_USER, DB_PASS);
      
      // set the PDO error mode to exception
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
      //echo "Connected successfully";

    }
    catch(PDOException $e){
      echo "Connection failed: " . $e->getMessage();
    }

    return $this->conn;
  }
}
?>