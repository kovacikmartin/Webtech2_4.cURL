$(document).ready(function(){

    $('#attendanceTable').DataTable({
    
        "searching": false,
        "paging": false,
        "info": false,
        "scrollY": "350px",
        "scrollCollapse": true,

        "columnDefs":[{
            "orderData": [0, 8],
            "targets": 0
        }, {
            "orderData": [0, 9],
            "targets": 0
        }]

    }).columns.adjust();
});

function showAttendanceModal($studentId, $lectureId){

    $.get("php/modalData.php?studentId="+$studentId+"&lectureId="+$lectureId, function(data){
        
        json = JSON.parse(data);

        $('#studentAttendanceModal').DataTable({

            "order": [],
            "searching": false,
            "paging": false,
            "info": false,
            "destroy": true,
            "data": json,
            "columns": [
                { "data": "action", "title": "Action"},
                { "data": "timestamp", "title": "Timestamp"}
            ]
        }).columns.adjust();

        $('#attendanceModal').modal('show');
    });
}

