<?php

    require_once("controllers/attendanceController.php");
    require_once("controllers/lectureController.php");
    require_once("controllers/studentController.php");

    $attendanceController = (new AttendaceController());
    $lectureController = (new LectureController());
    $studentController = (new StudentController());

    $lectures = $lectureController->getAllLectures();
    $students = $studentController->getAllStudents();

    $attendanceNumbers = $attendanceController->getLecturesAttendance();
?>

<div class="attendanceTableContainer">
<h3>Students' lecture attendance</h3>
    <table id="attendanceTable">
        <thead>
        <tr>
            <?php

                $lectureArrayChart = [];

                echo "<th>Name</th>";

                foreach($lectures as $order => $lecture){

                    echo "<th>" . $order+1 . ". lecture " . $lecture["date"] . "</th>";
                    array_push($lectureArrayChart, $order+1 . ". lecture " . $lecture["date"]);
                }

                echo "<th>Total attendances</th>";
                echo "<th>Total minutes</th>";
            ?>
        </tr>
        </thead>

        <tbody>
        <?php

            foreach($students as $key => $student){

                echo "<tr>";

                $multiSurname = explode(" ", $student["surname"]);
                if(array_key_exists(1, $multiSurname)){
                    
                    echo "<td data-sort=" . $multiSurname[1] . ">" . $student["name"] ." " . $student["surname"] . "</td>";
                }
                else{
                    echo "<td data-sort=" . $student["surname"] . ">" . $student["name"] ." " . $student["surname"] . "</td>";
                }
                
                $studentLectureAttendances = 0;
                $studentTotalMinutesSpent = 0;

                foreach($lectures as $order => $lecture){

                    $color = 'black';

                    $lastLeftTime = $attendanceController->lastLeftTime($lecture["id"]);
                    
                    $studentLectureTimeSpent = $attendanceController->getStudentLectureAttendanceTime($student["id"], $lecture["id"]);

                    if($studentLectureTimeSpent < 0){
                        $studentLectureTimeSpent += $lastLeftTime;
                        $color = 'orange';
                    }
                    
                    if($studentLectureTimeSpent !== 0){
                        $studentLectureAttendances++;
                    }

                    $studentTotalMinutesSpent += $studentLectureTimeSpent;
                    echo "<td style='color:".$color."' onclick=showAttendanceModal(" .$student['id']. ",".$lecture["id"].");>" . round($studentLectureTimeSpent, 2) . "</td>";
                }

                echo "<td>" . $studentLectureAttendances . "</td>";
                echo "<td>" . round($studentTotalMinutesSpent, 0) . "</td>";

                echo "</tr>";
            }
        ?>
        </tbody>
    </table>
</div>

<div class='modal' id='attendanceModal' tabindex='-1' role='dialog'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title'>Attendance</h5>
            </div>
            <div class='modal-body'>
                <table id='studentAttendanceModal'>
                </table>
                
            </div>
            <div class='modal-footer'>
                
            </div>
        </div>
    </div>
</div>

<div id="attendanceChart"></div>
<script>

    // code taken and adjusted from https://apexcharts.com/javascript-chart-demos/bar-charts/basic/
    var options = {
        series: [{
        data: <?php echo json_encode(array_column($attendanceNumbers, "students")); ?>,
        name: "Students"
        }],
        chart: {
        type: 'bar',
        height: 350,
        width: "100%",
        toolbar: {
            show: false
        }
        },
        plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: true,
        }
        },
        dataLabels: {
        enabled: false
        },
        xaxis: {
        categories: <?php echo json_encode($lectureArrayChart); ?>
        
        }
    };

    var chart = new ApexCharts(document.querySelector("#attendanceChart"), options);
    chart.render();

</script>