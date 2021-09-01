<?php

    require_once("../controllers/attendanceController.php");

    $attendanceController = (new AttendaceController());
    
    if(isset($_GET["studentId"]) && isset($_GET["lectureId"])){

        $studentId = $_GET["studentId"];
        $lectureId = $_GET["lectureId"];

        echo json_encode($attendanceController->showStudentAttendance($studentId, $lectureId));
    }
?>