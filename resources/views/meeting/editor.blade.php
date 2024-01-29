@extends('master')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
var stars =  document.getElementsByClassName("star");
let counter = 0;
    $(document).ready(function () {
        // add row for point discussed table
        $("#addrow").on("click", function () {
            var no = counter +2;
            var newRow = $('<tr>');
            var cols = "";
            cols += '<td class="col-1"><span id="rate-'+no+'-1" onclick="gfg(1,this.id)" class="star">★</span><span id="rate-'+no+'-2" onclick="gfg(2,this.id)" class="star">★</span><span id="rate-'+no+'-3" onclick="gfg(3,this.id)" class="star">★</span></td>';
            cols += '<td class="col-12"><input type="text" class="form-control" id="txtRemark-'+no+'" name="txtRemark-' + no + '" onchange="onRemarkChanged(this.id,this.value)"/><input type="hidden" id="txtRate-'+no+'" name="txtRate-'+no+'" value="0"></td>';
            cols += '<td class="col-sm-1"><button type="button" id="btnDel-'+no+'" class="ibtnDel btn btn-md btn-danger"><i class="fa fa-trash"></i></button></td>';
            cols += '<td class="col-sm-1"><button type="button" class="btn btn-success btn-md" id="myBtn" data-bs-toggle="modal" data-bs-target="#myModal" onclick="initDialog('+no+')">Task</button></td>'

            newRow.append(cols);
            $("table.order-list").append(newRow);
            counter++;
        });

        $("table.order-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();
            var idBtn = event.target.id;
            removeRowTaskTable(idBtn.split("-")[1]);
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
    function handler(e){

        const getSeconds = s => s.split(":").reduce((acc, curr) => acc * 60 + +curr, 0);
        var seconds1 = getSeconds(document.getElementById("startTime").value+":00");
        var seconds2 = getSeconds(document.getElementById("endTime").value+":00");

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

    function removeColon(s)
    {
        if (s.length == 4) 
            s= s.replace(":", "");
        
        if (s.length == 5) 
            s= s.replace(":", "");
        
        return parseInt(s);
    }
    
    // Main function which finds difference
    function diff( s1,  s2)
    {
        
        // change string (eg. 2:21 --> 221, 00:23 --> 23)
        time1 = removeColon(s1);
        
        time2 = removeColon(s2);
        
        console.log(time1+","+time2);
        // difference between hours
        hourDiff = parseInt((time2 / 100) - (time1 / 100) - 1);
        console.log(hourDiff);
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
    function initDialog(lineNumber)
    {
        document.getElementById('txtLineNumber').value = lineNumber;
        document.getElementById('txtRemarkDialog').value = document.getElementById('txtRemark-'+lineNumber).value

        console.log("task "+lineNumber+","+mapTasks.has(document.getElementById('txtLineNumber').value));
        if (mapTasks.has(document.getElementById('txtLineNumber').value))
        {
            document.getElementById('picSelect').value = mapTasks.get(document.getElementById('txtLineNumber').value).get("pic");
            notes.setData(mapTasks.get(document.getElementById('txtLineNumber').value).get("notes"));
            document.getElementById('dueDate').value = mapTasks.get(document.getElementById('txtLineNumber').value).get("dueDate");
        }
        else
        {
            document.getElementById('picSelect').value = "";
            notes.setData("<p></p>");
            document.getElementById('dueDate').valueAsDate = new Date();
        }
    }

    //save task to map
    function saveTask() 
    {
        $('#myModal').modal('hide');

        var added = true;
        if (mapTasks.has(document.getElementById('txtLineNumber').value))
            added = false;

        var mapTask = new Map();
        var lineNumber = document.getElementById('txtLineNumber').value;
        var compPic = document.getElementById('picSelect');
        var picName = compPic.options[compPic.selectedIndex].text;
        var dueDate = document.getElementById('dueDate').value;
        var remark = document.getElementById('txtRemarkDialog').value;
        mapTask.set("pic",document.getElementById('picSelect').value);
        mapTask.set("notes",notes.getData());
        mapTask.set("dueDate",dueDate);
        mapTask.set("picName",picName);
        mapTasks.set(document.getElementById('txtLineNumber').value,mapTask);

        if (added)
        {
            addRowTaskTable(remark, lineNumber, picName,dueDate,notes.getData());
        }
        else
        {
            console.log("update task table");
            updateTaskTable(lineNumber, remark, picName,dueDate,notes.getData());
        }

    }

    //when remark update then update the remark of task
    function onRemarkChanged(id,val) 
    {
        var lineNumber = id.split("-")[1];
        if (mapTasks.has(lineNumber))
        {
            var picName = mapTasks.get(lineNumber).get("picName");
            var dueDate = mapTasks.get(lineNumber).get("dueDate");
            var notes = mapTasks.get(lineNumber).get("notes");
            updateTaskTable(lineNumber, val, picName, dueDate, notes);
        }
    }

    //add row of task table
    function addRowTaskTable(remark, lineNumber, picName,dueDate,notes)
    {
        var newRow = $('<tr id="row-'+lineNumber+'">');
        let objectDate = new Date(dueDate);
        let day = objectDate.getDate();
        let month = objectDate.getMonth() + 1;
        let year = objectDate.getFullYear();
        var cols =  getCols(remark,picName, dueDate, notes, lineNumber) +"</tr>";
        newRow.append(cols);
        $("table.table-striped").append(newRow);
    }

    //remove row taskTable
    function removeRowTaskTable(id)
    {
        var idRow = "row-"+id;
        document.getElementById(idRow).remove();
        //$(idCol).parent().replaceWith("");
    }

    //update task table
    function updateTaskTable(id,remark,picName, dueDate, notes)
    {
        var cols = '<tr id="row-'+id+'">'
                    +getCols(remark,picName, dueDate, notes,id)
                    +'</tr>';
        var idCol = "td#col"+id;
        $(idCol).parent().replaceWith(cols);
    }

    //create coloumn for task table
    function getCols(remark,picName, dueDate, notes, id)
    { 
        let objectDate = new Date(dueDate);
        let day = objectDate.getDate();

        let month = objectDate.getMonth() + 1;
        let textMonth = month;
        if (month <= 9)
            textMonth = "0"+month;
        
        let year = objectDate.getFullYear();

        var cols = "";
        cols += '<td class="col-1"></td>';
        cols += '<td class="col-8" id="col'+id+'">'+remark +'<p>Note : </p>'+notes+'</td>';
        cols += '<td class="col-sm-3">'+picName+'</td>';
        cols += '<td class="col-sm-3">'+day+'-'+textMonth+'-'+year+'</td>';
        return cols;
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
        var topic = document.getElementById('txtTopic').value;
        var location = document.getElementById('txtLocation').value;
        var momDate = document.getElementById('momDate').value;
        var startTime = document.getElementById('startTime').value;
        var endTime = document.getElementById('endTime').value;
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
        for (let [key, value] of mapTasks) {
            let rate =  document.getElementById('txtRate-'+key).value;
            let remark =  document.getElementById('txtRemark-'+key).value;

            listPointDiscusseds = listPointDiscusseds.concat( [{'lineNumber': key,'rate':rate,'remark':remark}]);
            
            let pic = mapTasks.get(key).get('pic');
            let notes = mapTasks.get(key).get('notes');
            let dueDate = mapTasks.get(key).get('dueDate');
            listTask = listTask.concat( [{'index':key,'lineNumber': key,'pic':pic,'notes':notes, "dueDate":dueDate}])
        }

        $.ajax({
                type: 'post',
                data: {
                    id : idMeeting,
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
        var startTime = document.getElementById('startTime').value;
        var endTime = document.getElementById('endTime').value;
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
            var element =  document.getElementById('txtRate-'+key);
            if (typeof(element) != 'undefined' && element != null)
            {
                let rate =  document.getElementById('txtRate-'+key).value;
                let remark =  document.getElementById('txtRemark-'+key).value;

                listPointDiscusseds = listPointDiscusseds.concat( [{'lineNumber': key,'rate':rate,'remark':remark}]);
                
                let pic = mapTasks.get(key).get('pic');
                let notes = mapTasks.get(key).get('notes');
                let dueDate = mapTasks.get(key).get('dueDate');
                listTask = listTask.concat( [{'index':key,'lineNumber': key,'pic':pic,'notes':notes, "dueDate":dueDate}]);
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
                success: function(response) {
                    alert(response.message);
                    window.location = "{{ url('meeting')  }}";
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
                cols += '<td class="col-sm-1"><button type="button" class="btn btn-success btn-md" id="myBtn" data-bs-toggle="modal" data-bs-target="#myModal" onclick="initDialog('+no+')">Task</button></td>'

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

    function setTask(lineNumber,pic, picName,notes,dueDate)
    {
        var mapTask = new Map();
        var remark = document.getElementById('txtRemark-'+lineNumber).value;
        mapTask.set("pic",pic);
        mapTask.set("notes",notes);
        mapTask.set("dueDate",dueDate);
        mapTask.set("picName",picName);
        console.log("dueDate "+dueDate)
        mapTasks.set(lineNumber.toString(),mapTask);
        addRowTaskTable(remark, lineNumber, picName,dueDate,notes);
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
    $topic = "";
    $location = "";
    $momDate = Date("Y-m-d");
    $startTime = date("h:m");
    $endTime = date("h:m");
    $duration = "00:00";
    $partisipants = array();
    $tasks = array();
    $pointDiscuss = array();
    
    if (isset($data["meeting"]))
    {
        
        $id = $data["meeting"]->mom_id;
        $topic = $data["meeting"]->topic;
        $location = $data["meeting"]->location;
        $startTime = date_format($data["meeting"]->start_time,"H:i");
        $endTime = date_format($data["meeting"]->end_time, "H:i");
        $duration = $data["meeting"]->duration;
        
        for ($i = 0; $i < count($data["participants"]); $i++)
        {
            array_push($partisipants, $data["participants"][$i]->email);
            
        }

        $tasks = $data["tasks"];
        $pointDiscuss = $data["pointDiscuss"];

        

        for ($i = 0; $i < count($data["tasks"]); $i++)
        {

        }


        
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
                            <input type="time" id="startTime" name="startTime" onchange="handler(event);" value="<?php echo $startTime?>">
                            <label for="appt"> until </label>
                            <input type="time" id="endTime" name="endTime" onchange="handler(event);" value="<?php echo $endTime ?>">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="title"><b>Duration</b></label>
                        <input type="hidden" id="txtDuration" name="txtDuration" value="<?php echo $duration ?>">
                        <p id="timeDifference"><?php echo $duration ?></p>
                    </div>
                </div>
            </div>
            
            <br>
            <div class="form-group">
            <label for="appt"><b>Participants</b></label>
            <select name="paricipans" id="paricipans" multiple multiselect-search="true" class="form-control">
                <?php $users = $data["users"];
                    foreach ($users as $user)
                    {
                        $select = '<option value="'.$user->email. '" ';
                        if (in_array($user->email,$partisipants))
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
                            <td style="background-color:#e6e6e6">Remark</td>
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
                                <input type="text" id="txtRemark-1" name="txtRemark-1"  class="form-control"  onchange="onRemarkChanged(this.id,this.value)"/>
                                <input type="hidden" id="txtRate-1" name="txtRate-1" value="0">
                            </td>
                            <td class="col-sm-1"><a class="deleteRow"></a>
                            </td>
                            <td class="col-sm-1"> 
                                <button type="button" class="btn btn-success btn-md" id="myBtn" data-bs-toggle="modal" data-bs-target="#myModal" onclick="initDialog(1)">Task</button>
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
            <table id="tasTable" class="table table-striped"> 
                    <thead> 
                        <tr>
                            <td style="background-color:#e6e6e6">Task</td>
                            <td style="background-color:#e6e6e6">Remark</td>
                            <td style="background-color:#e6e6e6">PIC</td>
                            <td style="background-color:#e6e6e6">Due Date</td>
                        </tr>
                    </thead> 
                    <tbody>

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
    echo "<script> setTask(".$tasks[$i]->line_number.",'"
    .$tasks[$i]->pic."','".$tasks[$i]->name."','".$tasks[$i]->note.
    "','".$tasks[$i]->due_date."'); </script>";
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
                <select id="picSelect" class="form-control">
                    <option value="" selected="selected"> Select PIC </option>
                    <?php $users = $data["users"] ?>
                    @foreach ($users as $user)
                        <option value="{{ $user->email }}">{{ $user->name }}</option>
                    @endforeach
                    
                </select>
                <input type="hidden" id="txtLineNumber" name="txtLineNumber" value="0">
                <input type="hidden" id="txtRemarkDialog" name="txtLineNumber" value="0">
            </div>
            <br>
            <div><b>Due date :</b> <input type="date" class="form-control" id="dueDate" name="dueDate" placeholder="dd-mm-yyyy"></div>
            <br>
            <div><b>Note :</b></div>
            <textarea id="notes" rows="6" cols="150" class="form-control" value=""></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-success" type="submit" name="submit" value="Submit" onclick="saveTask()">Save</button>
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

//     $(function(){
//   $("#picSelect").select2({
//     dropdownParent: $("#myModal")
//   });
//  }); 
</script>
@endsection