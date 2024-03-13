@extends('master')

@section('content')

<script>
var stars =  document.getElementsByClassName("star");
let counter = 0;
let lastLineNumberTask = 0;
    
    $(document).ready(function () {
        // add row for point discussed table
        $("#addrow").on("click", function () {
            var no = counter +2;
            var newRow = $('<tr>');
            var cols = "";
            cols += '<td class="col-1"><span id="rate-'+no+'-1" onclick="gfg(1,this.id)" class="star">★</span><span id="rate-'+no+'-2" onclick="gfg(2,this.id)" class="star">★</span><span id="rate-'+no+'-3" onclick="gfg(3,this.id)" class="star">★</span></td>';
            cols += '<td class="col-12"><input type="text" class="form-control" maxlength="50" id="txtRemark-'+no+'" name="txtRemark-' + no + '" onchange="onRemarkChanged(this.id,this.value)"/><input type="hidden" id="txtRate-'+no+'" name="txtRate-'+no+'" value="0"></td>';
            cols += '<td class="col-sm-1"><button type="button" id="btnDel-'+no+'" class="ibtnDel btn btn-md btn-danger"><i class="fa fa-trash"></i></button></td>';
            cols += '<td class="col-sm-1"><button type="button" class="btn btn-success btn-md" id="myBtn" data-bs-toggle="modal" data-bs-target="#myModal" onclick="initDialog('+-1+','+no+')">Add Task</button></td>'

            newRow.append(cols);
            $("table.order-list").append(newRow);
            counter++;
        });

        $("table.order-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();
            var idBtn = event.target.id;
            removeRowTaskTable(-1,idBtn.split("-")[1]);
            //counter -= 1
        });

       
    });

    let notes;
    
    let output =  document.getElementById("output");
    
    // Funtion to update rating
    function gfg(n,id) 
    {
        remove(id);
        for (let i = 0; i < stars.length; i++) {
            if (n == 1) cls = "one";
            else if (n == 2) cls = "two";
            else if (n == 3) cls = "three";
            
            let idComp = stars[i].id;
            if (id.split("-")[1] == idComp.split("-")[1] && idComp.split("-")[2] <= n)
                stars[i].className = "star " + cls;
        }

        document.getElementById("txtRate-"+id.split("-")[1]).value = n;
    }
    
    // To remove the pre-applied styling
    function remove(id) {
        for (let i = 0; i < stars.length; i++) {
            let idComp = stars[i].id;
            if (id.split("-")[1] == idComp.split("-")[1])
                stars[i].className = "star";
        }
    }

    //calculate duration
    function handler(){

        const getSeconds = s => s.split(":").reduce((acc, curr) => acc * 60 + +curr, 0);
        var startTime = convertTime12to24(document.getElementById("startTime").value);
        var endTime = convertTime12to24(document.getElementById("endTime").value);
        var seconds1 = getSeconds(startTime+":00");
        var seconds2 = getSeconds(endTime+":00");

        console.log("start "+startTime+", "+endTime);

        var res = Math.abs(seconds2 - seconds1);

        var hours = Math.floor(res / 3600);
        var txtHours = hours;
        if (hours <= 9)
            txtHours = "0"+hours; 

        var minutes = Math.floor(res % 3600 / 60);
        var txtMinute = minutes;
        if (minutes <= 9)
            txtMinute = "0"+minutes;

        var seconds = res % 60;
        document.getElementById("timeDifference").innerHTML = txtHours + ":" + txtMinute;
        document.getElementById("txtDuration").value = txtHours + ":" + txtMinute;
        
    }

    function convertTime12to24(time12h) {
        let [hours, minutes, modifier] = time12h.match(/(\d+|pm|am)/gi);

        if (hours === '12') {
            hours = '00';
        }

        if (modifier.toLowerCase() === 'pm') {
            hours = parseInt(hours, 10) + 12;
        }

        let time24 = hours +":"+minutes;
        return time24;
    }

    function removeColon(s)
    {
        if (s.length == 4) 
            s= s.replace(":", "");
        
        if (s.length == 5) 
            s= s.replace(":", "");
        
        return parseInt(s);
    }
    
    // Main function which finds difference
    function diff(s1, s2)
    {
        
        // change string (eg. 2:21 --> 221, 00:23 --> 23)
        time1 = removeColon(s1);
        
        time2 = removeColon(s2);
        
        // difference between hours
        hourDiff = parseInt((time2 / 100) - (time1 / 100) - 1);

        // difference between minutes
        minDiff = parseInt(time2 % 100 + (60 - time1 % 100));
    
        if (minDiff >= 60) {
            hourDiff++;
            minDiff = minDiff - 60;
        }

        hour = (hourDiff).toString();
        if (hourDiff < 9)
            hour = "0"+(hourDiff).toString();

        minute = (minDiff).toString();
        if (minute < 9)
            minute = "0"+(minDiff).toString();
    
        // convert answer again in string with ':'
        res = hour+ ':' + minute;
        return res;
    }
    let mapTasks = new Map();
    let picSelect = document.getElementById('picSelect');

    //init dialog before show
    function initDialog(lineNumber, indexPointDisccuss)
    {
        console.log('init '+indexPointDisccuss+","+lineNumber);
        document.getElementById('txtIndexPoint').value = indexPointDisccuss;
        document.getElementById('txtLineNumber').value = lineNumber;
        document.getElementById('txtRemarkDialog').value = document.getElementById('txtRemark-'+indexPointDisccuss).value

        if (mapTasks.has(document.getElementById('txtIndexPoint').value))
        {
            console.log('a');
            let tasks = mapTasks.get(document.getElementById('txtIndexPoint').value);
            if (tasks.has(document.getElementById('txtLineNumber').value))
            {
                console.log('b');
                document.getElementById('txtStatus').value = tasks.get(document.getElementById('txtLineNumber').value).get("status");
                document.getElementById('txtRemarkTask').value = tasks.get(document.getElementById('txtLineNumber').value).get('remarkTask');
                document.getElementById('picSelect').value = tasks.get(document.getElementById('txtLineNumber').value).get("pic");
                notes.setData(tasks.get(document.getElementById('txtLineNumber').value).get("notes"));
                document.getElementById('dueDate').value = tasks.get(document.getElementById('txtLineNumber').value).get("dueDate");
            }
            else
            {
                document.getElementById('picSelect').value = "";
                notes.setData("<p></p>");
                document.getElementById('dueDate').valueAsDate = new Date();
            }
        }
        else
        {
            document.getElementById('picSelect').value = "";
            notes.setData("<p></p>");
            document.getElementById('dueDate').valueAsDate = new Date();
        }
        
        $(function(){
            $("#picSelect").select2({
                dropdownParent: $("#myModal")
            });
        }); 

    }

    //save task to map
    // function saveTask() 
    // {
        

    //     var added = true;
    //     if (mapTasks.has(document.getElementById('txtLineNumber').value))
    //         added = false;

    //     var mapTask = new Map();
        // var lineNumber = document.getElementById('txtLineNumber').value;
        // var compPic = document.getElementById('picSelect');
        // var picName = compPic.options[compPic.selectedIndex].text;
        // var dueDate = document.getElementById('dueDate').value;
        // var remark = document.getElementById('txtRemarkDialog').value;
        // var status = document.getElementById('txtStatus').value;
    //     mapTask.set("pic",document.getElementById('picSelect').value);
    //     mapTask.set("notes",notes.getData());
    //     mapTask.set("dueDate",dueDate);
    //     mapTask.set("picName",picName);
    //     mapTask.set("status",status);
    //     mapTasks.set(document.getElementById('txtLineNumber').value,mapTask);

    //     if (added)
    //     {
    //         addRowTaskTable(remark, lineNumber, picName,dueDate,notes.getData(), status);
    //     }
    //     else
    //     {
    //         updateTaskTable(lineNumber, remark, picName,dueDate,notes.getData(), status);
    //     }

    // }

    //when remark update then update the remark of task
    function onRemarkChanged(id,val) 
    {
        var lineNumber = id.split("-")[1];
        refreshTaskTable();
    }

    function refreshTaskTable()
    {
        $('#tb').empty();
        let no = 1;
        for (let [key,value] of mapTasks)
        {
            let remark = document.getElementById('txtRemark-'+key).value;
            let tasks = mapTasks.get(key);
            for (let [key2, value2] of tasks)
            {
                console.log("task");
                var picName = tasks.get(key2).get("picName");
                var dueDate = tasks.get(key2).get("dueDate");
                var notes = tasks.get(key2).get("notes");
                var status = tasks.get(key2).get("status");
                addRowTaskTable(no, remark, key2, key, picName,dueDate,notes, status);
                no++;
            }
        }
    }

    //add row of task table
    function addRowTaskTable(no, remark, lineNumber, indexPoint, picName,dueDate,notes, status)
    {
        var newRow = $('<tr id="row-'+lineNumber+'">');
        let objectDate = new Date(dueDate);
        let day = objectDate.getDate();
        let month = objectDate.getMonth() + 1;
        let year = objectDate.getFullYear();
        var cols =  getCols(no, remark,picName, dueDate, notes, lineNumber, indexPoint, status) +"</tr>";
        newRow.append(cols);
        $("table.table-striped").append(newRow);
    }

    //remove row taskTable
    function removeRowTaskTable(id, indexPoint)
    {
        if (id != -1)
        {
            var idRow = "row-"+id;
            document.getElementById(idRow).remove();
        }
        else
        {
            
            if (mapTask.has(indexPoint.toString()))
            {
                let tasks = mapTask.get(indexPoint.toString());
                for (let [key, value] of tasks)
                {
                    var idRow = "row-"+key;
                    document.getElementById(idRow).remove();
                }
            }
        }
        

        //$(idCol).parent().replaceWith("");
    }

    //update task table
    // function updateTaskTable(id, indexPoint, remark,picName, dueDate, notes, status)
    // {
    //     var cols = '<tr id="row-'+id+'">'
    //                 +getCols(remark,picName, dueDate, notes,id, indexPoint, status)
    //                 +'</tr>';
    //     var idCol = "td#col"+id;
    //     $(idCol).parent().replaceWith(cols);
    // }

    //create coloumn for task table
    function getCols(no, remark,picName, dueDate, notes, id, indexPoint, status)
    { 
        var mode = <?php echo $data["viewMode"];?>;
        let objectDate = new Date(dueDate);
        let day = objectDate.getDate();
        let statusName = getStatusName(status);

        let month = objectDate.getMonth() + 1;
        let textMonth = month;
        if (month <= 9)
            textMonth = "0"+month;
        
        let year = objectDate.getFullYear();

        var cols = "";
        var classNote = 'col-7';
        if (mode == 1)
            classNote = 'col-5';

        cols += '<td class="col-1">'+no+'</td>';
        cols += '<td class="'+classNote+'" id="col'+id+'">'+remark +'<p>Note : </p>'+notes+'</td>';
        cols += '<td class="col-2">'+picName+'</td>';
        cols += '<td class="col-1">'+day+'-'+textMonth+'-'+year+'</td>';

        if (mode == 1)
        {
            cols += '<td class="col-2">'+statusName+'</td>';
        }

        cols += '<td class="col-1">'
        +'<a href="#" onclick="initDialog('+id+','+indexPoint+')" data-bs-toggle="modal" data-bs-target="#myModal"> <span class="btn btn-success"><i class="bi bi-pencil-fill"></i></span></a> <br><br>'
        +'<a href="javascript:deleteTask('+id+','+indexPoint+')"> <span class="btn btn-danger"><i class="bi bi-trash3-fill"></i></span></a>'
        +'</td>';

        return cols;
    }

    function saveTask()
    {
        let indexPointDiscuss = document.getElementById('txtIndexPoint').value; 
        let lineNumber = document.getElementById('txtLineNumber').value;
        let compPic = document.getElementById('picSelect');
        let pic = document.getElementById('picSelect').value;
        let picName = compPic.options[compPic.selectedIndex].text;
        let dueDate = document.getElementById('dueDate').value;
        let remark = document.getElementById('txtRemarkDialog').value;
        let status = document.getElementById('txtStatus').value;
        let remarkTask = document.getElementById('txtRemarkTask').value;


        if (lineNumber == -1)
            addTask(indexPointDiscuss, pic, picName, notes.getData(), dueDate, status,remark,remarkTask);
        else
            updateTask(lineNumber, indexPointDiscuss, pic,picName, notes.getData(), dueDate, status,remark,remarkTask);
        
        $('#myModal').modal('hide');
    }

    function addTask(indexPointDiscuss, pic, picName, notes, dueDate, status, remark, remarkTask)
    {
        let id = lastLineNumberTask + 1;
        let task = new Map();
        let tasks = new Map();
        if (mapTasks.has(indexPointDiscuss.toString()))
            tasks = mapTasks.get(indexPointDiscuss.toString());
        
        task.set('pic',pic);
        task.set('picName',picName);
        task.set('notes',notes);
        task.set('dueDate',dueDate);
        task.set('status',status);
        task.set('remarkTask',remarkTask);

        tasks.set(id.toString(),task);
        mapTasks.set(indexPointDiscuss.toString(), tasks);
console.log('id '+id+","+indexPointDiscuss);
        refreshTaskTable();
        lastLineNumberTask++;
    }

    //delete task in taskdialog
    function deleteTask(lineNumber, indexPointDiscuss)
    {
        if (mapTasks.has(indexPointDiscuss.toString()))
        {
            let tasks = mapTasks.get(indexPointDiscuss.toString());
            tasks.delete(lineNumber.toString());
            mapTasks.set(indexPointDiscuss.toString(), tasks);

            refreshTaskTable();
        }
    }

    function updateTask(lineNumber, indexPointDiscuss, pic,picName, notes, dueDate, status, remark, remarkTask)
    {
        if (mapTasks.has(indexPointDiscuss.toString()))
        {
            let tasks = mapTasks.get(indexPointDiscuss.toString());
            if (tasks.has(lineNumber.toString()))
            {
                let task = tasks.get(lineNumber.toString());
                task.set('pic',pic);
                task.set('picName',picName);
                task.set('notes',notes);
                task.set('dueDate',dueDate);
                task.set('status',status);
                task.set('remarkTask',remarkTask);

                tasks.set(lineNumber.toString(), task);
                mapTasks.set(indexPointDiscuss.toString(), tasks);                
            }
            mapTasks.set(indexPointDiscuss.toString(), tasks);
            refreshTaskTable();
        }
    }

    
    function getStatusName(status)
    {
        let statusName = "";
        switch(status) {
            case '0':
                statusName = "On Progress";
                break;
            case '1':
                statusName = "Done";
                break;
            default:
                statusName = "Unknown Status"
        }

        return statusName;
    }

    function save(mode)
    {

        if (verifyForm())
        {
            if(mode == 0)
                addMeeting();
            else
                updateMeeting();
        }
    }

    function verifyForm()
    {
        var topic = document.getElementById('txtTopic').value;
        if (topic == '')
        {
            alert("Topic masih kosong");
            document.getElementById('txtTopic').focus();
            return false;
        }
        
        var location = document.getElementById('txtLocation').value;
        if (location == '')
        {
            alert("Location masih kosong");
            document.getElementById('txtLocation').focus();
            return false;
        }

        const partisipans = [];
        const select = document.getElementById('paricipans');
        for (const option of select.options) {
            if (option.selected) {
                partisipans.push(option.value);
            }
        }

        if (partisipans.length == 0)
        {
            alert("Participants masih kosong");
            return false;
        }

        for (let i = 1; i <= counter + 1; i++)
        {
            if (document.getElementById('txtRemark-'+i) != null)
            {
                if (document.getElementById('txtRemark-'+i).value == '')
                {
                    alert("Remark masih kosong");
                    document.getElementById('txtRemark-'+i).focus();
                    return false;
                }
            }
        }

        if (mapTasks.size == 0)
        {
            alert("Task masih kosong");
            return false;
        }

        for (let i = 1; i <= counter + 2; i++)
        {
            let lnNumber = i.toString();
            if (mapTasks.has(lnNumber))
            {
                if (mapTasks.get(lnNumber).get('pic') == '')
                {
                    alert("PIC untuk task "+document.getElementById('txtRemark-'+i).value+" masih kosong");
                    return false;
                }
            }
            else
            {
                if (document.getElementById('txtRemark-'+i) != null)
                {
                    alert("Task untuk point discuss "+document.getElementById('txtRemark-'+i).value+" masih kosong");
                    document.getElementById('txtRemark-'+i).focus();
                    return false;
                }
            }
        } 

        return true;
    }

    function updateMeeting()
    {
        var idMeeting = document.getElementById('txtId').value;
        var idCalendar = document.getElementById('txtIdCalendar').value;
        var topic = document.getElementById('txtTopic').value;
        var location = document.getElementById('txtLocation').value;
        var momDate = document.getElementById('momDate').value;
        var startTime = convertTime12to24(document.getElementById("startTime").value);
        var endTime = convertTime12to24(document.getElementById("endTime").value);
        var duration = document.getElementById('txtDuration').value;
        const partisipans = [];
        const select = document.getElementById('paricipans');
        for (const option of select.options) {
            if (option.selected) {
                partisipans.push(option.value);
            }
        }

        //set the poin discusc and task
        var listPointDiscusseds = new Array;
        var listTask = new Array();
        for (let [key, value] of mapTasks) 
        {
            let rate =  document.getElementById('txtRate-'+key).value;
            let remark =  document.getElementById('txtRemark-'+key).value;

            listPointDiscusseds = listPointDiscusseds.concat( [{'lineNumber': key,'rate':rate,'remark':remark}]);
            
            let tasks = mapTasks.get(key);
            for (let [key2, value2] of tasks)
            {
                let pic = tasks.get(key2).get('pic');
                let notes = tasks.get(key2).get('notes');
                let dueDate = tasks.get(key2).get('dueDate');
                let status = tasks.get(key2).get('status');
                let remarkTask = tasks.get(key2).get('remarkTask');
                listTask = listTask.concat( [{'index':key,'lineNumber': key2,'pic':pic,'notes':notes, "dueDate":dueDate, "status":status, "remarkTask":remarkTask}])
            }
        }

        $.ajax({
                type: 'post',
                data: {
                    id : idMeeting,
                    idCalendar : idCalendar,
                    topic : topic,
                    location : location,
                    momDate : momDate,
                    startTime : startTime,
                    endTime : endTime,
                    duration : duration,
                    partisipans : partisipans,
                    listPointDiscusseds : listPointDiscusseds,
                    listTask : listTask,
                },
                url: "{{ url('meeting/edit') }}",
                success: function(response) {
                    alert(response.message);
                    window.location = "{{ url('meeting')  }}";
                }

            })
    }

    function addMeeting() 
    {
        var topic = document.getElementById('txtTopic').value;
        var location = document.getElementById('txtLocation').value;
        var momDate = document.getElementById('momDate').value;
        var startTime = convertTime12to24(document.getElementById("startTime").value);
        var endTime = convertTime12to24(document.getElementById("endTime").value);
        var duration = document.getElementById('txtDuration').value;
        const partisipans = [];
        const select = document.getElementById('paricipans');
        for (const option of select.options) {
            if (option.selected) {
                partisipans.push(option.value);
            }
        }

        //set the poin discusc and task
        var listPointDiscusseds = new Array;
        var listTask = new Array();
        for (let [key, value] of mapTasks) 
        {
            let rate =  document.getElementById('txtRate-'+key).value;
            let remark =  document.getElementById('txtRemark-'+key).value;

            listPointDiscusseds = listPointDiscusseds.concat( [{'lineNumber': key,'rate':rate,'remark':remark}]);    
            let tasks = mapTasks.get(key);
            for (let [key2, value2] of tasks)
            {
                var element =  document.getElementById('txtRate-'+key);
                if (typeof(element) != 'undefined' && element != null)
                {
                    let pic = tasks.get(key2).get('pic');
                    let notes = tasks.get(key2).get('notes');
                    let dueDate = tasks.get(key2).get('dueDate');
                    listTask = listTask.concat( [{'index':key,'lineNumber': key2,'pic':pic,'notes':notes, "dueDate":dueDate}]);
                    console.log('add '+key+","+key2);
                }
            }
        }

        $.ajax({
                type: 'post',
                data: {
                    topic : topic,
                    location : location,
                    momDate : momDate,
                    startTime : startTime,
                    endTime : endTime,
                    duration : duration,
                    partisipans : partisipans,
                    listPointDiscusseds : listPointDiscusseds,
                    listTask : listTask,
                },
                url: "{{ url('meeting/add') }}",
                success: function(response) 
                {
                    alert(response.message);
                    if (response.success)
                    {
                        window.location = "{{ url('meeting')  }}";
                    }
                }
            })
    }

    function setPointDiscuss(no,remark,rate)
    {
        if (no > 1)
        {
            var newRow = $('<tr>');
                var cols = "";
                cols += '<td class="col-1"><span id="rate-'+no+'-1" onclick="gfg(1,this.id)" class="star">★</span><span id="rate-'+no+'-2" onclick="gfg(2,this.id)" class="star">★</span><span id="rate-'+no+'-3" onclick="gfg(3,this.id)" class="star">★</span></td>';
                cols += '<td class="col-12"><input type="text" class="form-control" id="txtRemark-'+no+'" name="txtRemark-' + no + '" onchange="onRemarkChanged(this.id,this.value)" value="'+remark+'" /><input type="hidden" id="txtRate-'+no+'" name="txtRate-'+no+'" value="0"></td>';
                cols += '<td class="col-sm-1"><button type="button" class="ibtnDel btn btn-md btn-danger"><i class="fa fa-trash"></i></button></td>';
                cols += '<td class="col-sm-1"><button type="button" class="btn btn-success btn-md" id="myBtn" data-bs-toggle="modal" data-bs-target="#myModal" onclick="initDialog('+-1+','+no+')">Add Task</button></td>'

                newRow.append(cols);
                $("table.order-list").append(newRow);
                if (no -2 > counter)
                    counter = no - 2;

                var id = "rate-"+no+"-"+rate;
                gfg(rate,id);
        }
        else
        {
            var id = "rate-1-"+rate;
            document.getElementById('txtRemark-'+no).value = remark;
            gfg(rate,id);
        }
    }

    function setTask(lineNumber, indexPointDiscussed, pic, picName,notes,dueDate, status, remarkTask)
    {
        var mapTask = new Map();
        var remark = document.getElementById('txtRemark-'+indexPointDiscussed).value;

        let tasks = new Map();
        if (mapTasks.has(indexPointDiscussed.toString()))
            tasks = mapTasks.get(indexPointDiscussed.toString());
        
        let task = new Map();
        task.set("pic",pic);
        task.set("notes",notes);
        task.set("dueDate",dueDate);
        task.set("picName",picName);
        task.set("status",status);
        task.set("remarkTask",remarkTask);

        tasks.set(lineNumber.toString(), task);
        mapTasks.set(indexPointDiscussed.toString(),tasks);
        if (lastLineNumberTask < lineNumber)
            lastLineNumberTask = parseInt(lineNumber);
console.log('set task '+lineNumber+","+indexPointDiscussed);

        refreshTaskTable();
    }
</script>
<div>
    <div class="card-header-rounded">
        <h4>Minutes of the Meeting</h4>
    </div>
    
    <div class="card-content-rounded" >
    <?php 
    date_default_timezone_set('Asia/Jakarta');
    $id = "";
    $idCalendar = "";
    $topic = "";
    $location = "";
    $momDate = Date("Y-m-d");
    $startTime = Date("h:m");
    $endTime = Date("h:m");
    $duration = "00:00";
    $partisipants = array();
    $tasks = array();
    $pointDiscuss = array();
    $createdBy = "";
    $createdOn = "";
    $updatedBy = "";
    $updatedOn = "";
    
    if (isset($data["meeting"]))
    {
        
        $id = $data["meeting"]->mom_id;
        $idCalendar = $data["meeting"]->calendar_id;
        $topic = $data["meeting"]->topic;
        $location = $data["meeting"]->location;
        $startTime = date_format($data["meeting"]->start_time,"H:m");
        $endTime = date_format($data["meeting"]->end_time, "H:m");
        $duration = $data["meeting"]->duration;
        $users = $data["users"];
        $createdBy = $data["meeting"]->created_by;
        $updatedBy =   $data["meeting"]->updated_by;
        foreach ($users as $user)
        {
            if ($user->email == $data["meeting"]->updated_by)
                $updatedBy = $user->name;
            
            if ($user->email == $data["meeting"]->created_by)
                $createdBy = $user->name;
            
        }

        
        $updatedOn =  date_format($data["meeting"]->updated_at, "d M Y H:m");
        $createdOn =  date_format($data["meeting"]->created_at, "d M Y H:m");
        
        for ($i = 0; $i < count($data["participants"]); $i++)
        {
            array_push($partisipants, $data["participants"][$i]->user_id);
        }

        $tasks = $data["tasks"];
        $pointDiscuss = $data["pointDiscuss"];        
    }
                            
    ?>
        <form action="#">
        @csrf
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="title"><b>Topic</b></label>
                        <textarea id="txtTopic" name="txtTopic" class="form-control" rows="2" cols="50" required><?php echo $topic; ?></textarea>
                        
                    </div>
                    <div class="form-group">
                        <label for="title"><b>Location</b></label>
                        <input type="hidden" id="txtId" value=" <?php echo $id; ?>"/>
                        <input type="hidden" id="txtIdCalendar" value=" <?php echo $idCalendar; ?>"/>
                        <input type="text" class="form-control" id="txtLocation" name="txtLocation" value="<?php echo $location; ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="title"><b>Date</b></label>
                        <input type="date" class="form-control" id="momDate" name="momDate" placeholder="dd-mm-yyyy" value="<?php echo $momDate;?>">
                    </div>
                    <div>
                        <label for="appt"><b>Time</b></label>
                        <div>
                            <label for="appt">start</label>
                            <input id="startTime" type="text" class="time"/>
                            <label for="appt"> until </label>
                            <input id="endTime" type="text" class="time"/>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="title"><b>Duration</b></label>
                        <input type="hidden" id="txtDuration" name="txtDuration" value="<?php echo $duration ?>">
                        <p id="timeDifference"><?php echo $duration ?></p>
                        
                    </div>
                    <?php if ($data["viewMode"] == 1){?>
                        <div class="form-group">
                        <label for="title"><b>Created by </b></label>
                        <label for="title"><?php echo $createdBy ?> <b>at</b> <?php echo $createdOn ?> </label>
                        <br>
                        <label for="title"><b>Updated by </b></label>
                        <label for="title"><?php echo $updatedBy ?> <b>at</b> <?php echo $updatedOn ?> </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
            
            <br>
            <div class="form-group">
            <label for="appt"><b>Participants</b></label>
            <select name="paricipans" id="paricipans" multiple multiselect-search="true" class="form-control">
                <?php $users = $data["users"];
                    foreach ($users as $user)
                    {
                        $select = '<option value="'.$user->id. '" ';
                        if (in_array($user->id,$partisipants))
                        {
                            $select .= 'selected="true" ';
                        } 
                        $select .= '>'.$user->name .'</option>';
                        echo $select;
                    }
                ?>
            </select>
            </div>
            <br>

            <div class="fixTableHead"> 
                <table id="myTable" class="table order-list"> 
                    <thead> 
                        <tr>
                            <td style="background-color:#e6e6e6">Point Discussed</td>
                            <td style="background-color:#e6e6e6; vertical-align: middle;">Remark</td>
                            <td style="background-color:#e6e6e6"></td>
                            <td style="background-color:#e6e6e6"></td>
                        </tr>
                    </thead> 
            
                    <tbody> 
                        <tr>
                            <td class="col-1" style="font-size:16px"> 
                                <span id="rate-1-1" onclick="gfg(1,this.id)" class="star">★</span><span id="rate-1-2" onclick="gfg(2,this.id)" class="star">★</span><span id="rate-1-3" onclick="gfg(3,this.id)" class="star">★</span></td>
                            </td>
                            <td class="col-12">
                                <input type="text" maxlength="50" id="txtRemark-1" name="txtRemark-1"  class="form-control"  onchange="onRemarkChanged(this.id,this.value)"/>
                                <input type="hidden" id="txtRate-1" name="txtRate-1" value="0">
                            </td>
                            <td class="col-sm-1"><a class="deleteRow"></a>
                            </td>
                            <td class="col-sm-1"> 
                                <button type="button" class="btn btn-success btn-md" id="myBtn" data-bs-toggle="modal" data-bs-target="#myModal" onclick="initDialog(-1,1)">Add Task</button>
                            </td>
                        </tr>
                    </tbody> 
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right;">
                                <input type="button" class="btn btn-primary" id="addrow" value="Add Row" />
                            </td>
                        </tr>
                    </tfoot>
                </table> 
            </div> 
            <br>
            <div class="fixTableHead">
            <table id="taskTable" class="table table-striped"> 
                    <thead> 
                        <tr>
                            <td style="background-color:#e6e6e6; vertical-align: middle;">Action Plan</td>
                            <td style="background-color:#e6e6e6; vertical-align: middle;" >Remark</td>
                            <td style="background-color:#e6e6e6; vertical-align: middle;">PIC</td>
                            <td style="background-color:#e6e6e6; vertical-align: middle;">Due Date</td>
                            <?php if ($data["viewMode"] == 1){?>
                                <td style="background-color:#e6e6e6; vertical-align: middle;">Status</td>
                            <?php }?>
                            <td style="background-color:#e6e6e6; vertical-align: middle;">Action</td>
                        </tr>
                    </thead> 
                    <tbody id="tb">
                    </tbody>
            </table>
            </div>    
            <br>
            <button type="button" class="btn btn-primary" onclick="save(<?php echo $data["viewMode"] ?>)">Save</button>
        </form>
    </div>
</div>
<?php 
for ($i = 0; $i < count($pointDiscuss); $i++)
{
    echo '<script> setPointDiscuss('.$pointDiscuss[$i]->line_number.',\''
    .$pointDiscuss[$i]->remark.'\','.$pointDiscuss[$i]->rate.'); </script>';
}
for ($i = 0; $i < count($tasks); $i++)
{
    echo "<script> setTask('".$tasks[$i]->line_number
    ."','".$tasks[$i]->point_discussed_index."','".$tasks[$i]->pic
    ."','".$tasks[$i]->name."','".$tasks[$i]->note.
    "','".$tasks[$i]->due_date."','".$tasks[$i]->status."','".$tasks[$i]->remark."'); </script>";
}
?>
<div class="modal fade" id="myModal" role="dialog" data-bs-focus="false" >
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Task</h5>
        </div>
        <div class="modal-body" >
            <div><b>PIC :</b> 
                <select id="picSelect" style="width: 50%">
                    <option value="" selected="selected"> Select PIC </option>
                    <?php $users = $data["users"] ?>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                    
                </select>
                <input type="hidden" id="txtIndexPoint" name="txtIndexPoint" value="1">
                <input type="hidden" id="txtLineNumber" name="txtLineNumber" value="-1">
                <input type="hidden" id="txtRemarkDialog" name="txtRemarkDialog" value="0">
                <input type="hidden" id="txtRemarkTask" name="txtRemarkTask" value="">
                <input type="hidden" id="txtStatus" name="txtStatus" value="<?php echo App\Models\meeting\ActionPlan::STATUS_ON_PROGRESS ?>">
            </div>
            <br>
            <div><b>Due date :</b> <input type="date" class="form-control" id="dueDate" name="dueDate" placeholder="dd-mm-yyyy"></div>
            <br>
            <div><b>Note :</b></div>
            <textarea id="notes" rows="6" cols="150" class="form-control" value=""></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-success" onclick="saveTask()">Save</button>
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
</div>
   

<script>
    //set area as text rich
    ClassicEditor
    .create(document.querySelector("#notes"))
    .then( newEditor => {
        notes = newEditor;
    } )
    .catch(error => {
        console.error( error );
    } );

    $(function() {
        $('#startTime').timepicker();
        $('#endTime').timepicker();

        $('#startTime').on('changeTime', function() {
            handler();
        });
        $('#endTime').on('changeTime', function() {
            handler();
        });
    });

    $(document).ready(function () {
        var dateMom = "<?php echo $momDate; ?>";
        var startTime = "<?php echo $startTime; ?>";
        var endTime = "<?php echo $endTime; ?>";
        var mode = <?php echo $data["viewMode"];?>;
        console.log("set start "+startTime+",  "+endTime);
        if (mode == 1)
        {
            $('#startTime').timepicker('setTime', new Date(dateMom +" "+startTime));
            $('#endTime').timepicker('setTime', new Date(dateMom +" "+endTime));
        }
        else
        {
            $('#startTime').timepicker('setTime', new Date());
            $('#endTime').timepicker('setTime', new Date());
        }
    });

   
</script>
@endsection