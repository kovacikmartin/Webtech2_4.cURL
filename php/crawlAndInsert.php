<?php
    require_once("controllers/studentController.php");
    require_once("controllers/lectureController.php");
    require_once("controllers/attendanceController.php");

    $studentController = (new StudentController());
    $lectureController = (new LectureController());
    $attendanceController = (new AttendaceController());

    $gitCurl = curl_init();

    curl_setopt($gitCurl, CURLOPT_URL, "https://github.com/apps4webte/curldata2021");
    curl_setopt($gitCurl, CURLOPT_RETURNTRANSFER, 1);

    $gitCurlOutput = curl_exec($gitCurl);

    curl_close($gitCurl);

    $parsedGitOutput = new DOMDocument();

    // @ to ignore warnings such as <svg> tag not recognized by loadHTML
    @$parsedGitOutput->loadHTML($gitCurlOutput);

    $csvXpath = new DOMXPath($parsedGitOutput);
    $csvFiles = $csvXpath->query("//*[contains(@class, 'js-navigation-open Link--primary')]");

    $dbLectures = $lectureController->getNumberOfLectures();
    $gitLectures = $csvFiles->length;
    
    if($gitLectures > $dbLectures){
        
        foreach($csvFiles as $key => $csvFile){

            // process only the attendance files not already in db
            if($key+1 > $dbLectures){
            
                $csvLink = $csvFile->nodeValue;
                
                $csvCurl = curl_init();

                curl_setopt($csvCurl, CURLOPT_URL, "https://raw.githubusercontent.com/apps4webte/curldata2021/main/" . $csvLink);
                curl_setopt($csvCurl, CURLOPT_RETURNTRANSFER, 1);

                $csvCurlOutput = curl_exec($csvCurl);
                $csvCurlOutput = mb_convert_encoding($csvCurlOutput, "UTF-8", "UTF-16");

                curl_close($csvCurl);

                $rows = explode("\n", $csvCurlOutput);
                
                foreach($rows as $row => $value){

                    if($row === 0){
                        continue;
                    }

                    if(!empty($value)){
                        
                        $value = explode("\t", $value);

                        // change / in date to -
                        $value[2] = explode(', ', $value[2], 2);
                        $date = str_replace("/", "-", $value[2][0]);
                    }
                    
                    // insert lecture
                    if($row === 1){
                        
                        // try: dd/mm/yyyy; catch: mm/dd/yyyy
                        try{
                            $date = new DateTime($date);
                        }
                        catch(Exception $e){
                            $date = DateTime::createFromFormat("m-d-Y", $date);
                        }

                        // for table's date type
                        $date = $date->format("Y-m-d");

                        $lectureController->insertLecture($date);

                        $lectureId = $lectureController->getLectureId($date);
                    }
                    
                    // insert students and attendance
                    if($row > 0 && !empty($value)){
                        
                        // in case of AM/PM format
                        $timestamp = explode(" ", $value[2][1], 2);
                        
                        // check if AM/PM present, if yes then convert to 24h format
                        if(array_key_exists(1, $timestamp)){
                            $timestamp[0] = date("H:i:s" , strtotime($timestamp[0]));
                        }

                        $fullName = explode(' ', $value[0], 2);
                        $name = $fullName[0];
                        $surname = $fullName[1];
                    
                        $studentController->insertStudent($name, $surname);
                        $studentId = $studentController->getStudentId($name, $surname);
                        
                        $attendanceController->insertIntoAttendance($studentId, $lectureId, $value[1], $timestamp[0]);
                    }
                }
            }
        }
    }
?>