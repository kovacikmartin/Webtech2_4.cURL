<?php

    require_once("database.php");

    class LectureController{

        private PDO $conn;

        public function __construct()
        {
            $this->conn = (new Database())->getConnection();
        }

        public function insertLecture($date){

            try{
                $sql = "INSERT INTO t_lecture(date)
                                    VALUES(?)";
            
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
                $this->conn->prepare($sql)->execute([$date]);
                
            }
            catch(PDOException $e){
        
                if($e->getCode() === '23000'){
    
                    // unique constraint violated, lecture already in the table
                }
                else{
                    echo "<div class='alert alert-danger' role='alert'>
                                Sorry, there was an error inserting lecture." .$e->getMessage()."
                          </div>";
                }
            }
        }

        public function getNumberOfLectures(){

            $num = null;

            try{

                $sql = "SELECT COUNT(*) FROM t_lecture";
                $num = $this->conn->query($sql)->fetchColumn();
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                                Sorry, there was an error in getting the number of lectures." .$e->getMessage()."
                          </div>";
            }

            return $num;
        }

        public function getLectureId($date){

            $id = null;

            try{

                $sql = "SELECT id FROM t_lecture WHERE date=?";

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
                $stmnt = $this->conn->prepare($sql);
                $stmnt->execute([$date]);

                $id = $stmnt->fetchColumn();
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                                Sorry, there was an error while getting the ID of a user." .$e->getMessage()."
                          </div>";
            }

            return $id;
        }

        public function getAllLectures(){
            
            $lectures = null;

            try{

                $sql = "SELECT id, date FROM t_lecture ORDER BY date ASC";

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmnt = $this->conn->query($sql);

                $lectures = $stmnt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                            Sorry, there was an error while getting all lectures." .$e->getMessage()."
                        </div>";
            }

            return $lectures;
        }
    }
?>