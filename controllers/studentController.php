<?php
     require_once("database.php");

     class StudentController{

        private PDO $conn;

        public function __construct(){
            $this->conn = (new Database())->getConnection();
        }

        public function insertStudent($name, $surname){

            try{
                $sql = "INSERT INTO t_student(name, surname)
                                    VALUES(?, ?)";
            
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
                $this->conn->prepare($sql)->execute([$name, $surname]);
                
            }
            catch(PDOException $e){
        
                if($e->getCode() === '23000'){
    
                    // unique constraint violated, student already in the table
                }
                else{
                    echo "<div class='alert alert-danger' role='alert'>
                                Sorry, there was an error inserting user." .$e->getMessage()."
                          </div>";
                }
            }
        }

        public function getStudentId($name, $surname){

            $id = null;

            try{

                $sql = "SELECT id FROM t_student WHERE name=? AND surname=?";

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
                $stmnt = $this->conn->prepare($sql);
                $stmnt->execute([$name, $surname]);

                $id = $stmnt->fetchColumn();
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                                Sorry, there was an error while getting the ID of a user." .$e->getMessage()."
                          </div>";
            }

            return $id;
        }

        public function getAllStudents(){
            
            $students = null;

            try{

                $sql = "SELECT id, name, surname FROM t_student";

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
                
                $stmnt = $this->conn->query($sql);

                $students = $stmnt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                                Sorry, there was an error while getting all students." .$e->getMessage()."
                          </div>";
            }

            return $students;
        }
     }
?>