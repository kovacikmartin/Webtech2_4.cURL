<?php
    require_once("database.php");

    class AttendaceController{

        private PDO $conn;

        public function __construct()
        {
            $this->conn = (new Database())->getConnection();
        }


        public function insertIntoAttendance($studentId, $lectureId, $action, $timestamp){
            
            try{
                $sql = "INSERT INTO t_attendance(student_id, lecture_id, action, timestamp)
                                    VALUES(?, ?, ?, ?)";
            
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
                $this->conn->prepare($sql)->execute([$studentId, $lectureId, $action, $timestamp]);
            }
            catch(PDOException $e){
        
                if($e->getCode() === '23000'){
    
                    // unique constraint violated, student already in the table
                }
                else{
                    echo "<div class='alert alert-danger' role='alert'>
                                Sorry, there was an error inserting attendace." .$e->getMessage()."
                          </div>";
                }
            }

        }

        public function getFullAttendance(){

            $attendance = null;

            try{

                $sql = 
                "
                    SELECT
                         s.name
                        ,s.surname
                        ,l.date
                        ,a.action
                        ,a.timestamp
                    FROM t_student    s
                    JOIN t_attendance a ON s.id = a.student_id
                    JOIN t_lecture    l ON l.id = a.lecture_id
                ";

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
                $stmnt = $this->conn->query($sql);
                $attendance = $stmnt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                        Sorry, there was an error retrieving full attendace." .$e->getMessage()."
                      </div>";
            }

            return $attendance;
        }

        private function timestampToMinutes($timestamp){

            $timestamp = explode(':', $timestamp);
            $minutes = (($timestamp[0] * 60) + ($timestamp[1]) + ($timestamp[2] / 60));

            return $minutes;
        }

        public function lastLeftTime($lectureId){

            $time = null;
            try{

                $sql = "SELECT MAX(timestamp) FROM t_attendance WHERE lecture_id=?";

                $stmnt = $this->conn->prepare($sql);
                $stmnt->execute([$lectureId]);

                $time = $stmnt->fetchColumn();
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                            Sorry, there was an error retrieving last leaving time." .$e->getMessage()."
                        </div>";

            }

            return $this->timestampToMinutes($time);
        }

        public function getStudentLectureAttendanceTime($studentId, $lectureId){

            $attendance = null;

            try{

                $sql = 
                "
                    SELECT
                         a.action
                        ,a.timestamp
                    FROM t_attendance a 
                    WHERE a.student_id = ?
                    AND a.lecture_id = ?
                ";

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                   
                $stmnt = $this->conn->prepare($sql);
                $stmnt->execute([$studentId, $lectureId]);

                $attendance = $stmnt->fetchAll(PDO::FETCH_ASSOC);

                $joinedMinutes = 0;
                $leftMinutes = 0;

                foreach($attendance as $key => $action){
                    if(str_contains($action["action"], "Joined")){

                        $joinedMinutes += $this->timestampToMinutes($action["timestamp"]);
                    }
                    elseif(str_contains($action["action"], "Left")){

                        $leftMinutes += $this->timestampToMinutes($action["timestamp"]);
                    }
                }

                return $leftMinutes-$joinedMinutes;
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                        Sorry, there was an error retrieving student's (" . $studentId. ") attendace." .$e->getMessage()."
                      </div>";
            }
        }

        public function getLecturesAttendance(){

            $attendanceNumbers = null;

            try{

                $sql = "SELECT COUNT(DISTINCT(student_id)) AS 'students' FROM t_attendance GROUP BY lecture_id ORDER BY lecture_id";

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmnt = $this->conn->query($sql);

                $attendanceNumbers = $stmnt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                        Sorry, there was an error retrieving attendance numbers for chart." .$e->getMessage()."
                      </div>";
            }

            return $attendanceNumbers;
        }

        public function showStudentAttendance($studentId, $lectureId){

            $attendance = null;

            try{

                $sql = 
                "
                    SELECT
                         action
                        ,timestamp
                    FROM t_attendance
                    WHERE student_id = ?
                    AND   lecture_id = ?
                    ORDER BY timestamp
                ";

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                   
                $stmnt = $this->conn->prepare($sql);
                $stmnt->execute([$studentId, $lectureId]);

                $attendance = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    
            }
            catch(PDOException $e){
                echo "<div class='alert alert-danger' role='alert'>
                        Sorry, there was an error retrieving student's (" . $studentId. ") attendace." .$e->getMessage()."
                      </div>";
            }

            return $attendance;
        }
    }
?>