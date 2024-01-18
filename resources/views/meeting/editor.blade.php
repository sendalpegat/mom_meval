@extends('master')

@section('content')
<div>
    <div class="card-header-rounded">
        <h4>Minutes of the Meeting</h4>
    </div>
    
    <div class="card-content-rounded" >
       
        <form action="" method="post">
        @csrf
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="title"><b>Topic</b></label>
                        <textarea id="topic" name="topic" class="form-control" rows="2" cols="50" required></textarea>
                        
                    </div>
                    <div class="form-group">
                        <label for="title"><b>Location</b></label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="title"><b>Date</b></label>
                        <input type="date" class="form-control" id="momDate" name="momDate">
                    </div>
                    <div>
                        <label for="appt"><b>Time</b></label>
                        <div>
                            <label for="appt">start</label>
                            <input type="time" id="startTime" name="startTime" onchange="handler(event);">
                            <label for="appt"> until </label>
                            <input type="time" id="endTime" name="endTime" onchange="handler(event);">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="title"><b>Duration</b></label>
                        <p id="timeDifference">--:--</p>
                    </div>
                </div>
            </div>
            
            <br>
            <div class="form-group">
            <label for="appt"><b>Participants</b></label>
            <select name="cars" id="cars" multiple multiselect-search="true" class="form-control">
                <option value="1">Audi</option>
                <option selected value="2">BMW</option>
                <option selected value="3">Mercedes</option>
                <option value="4">Volvo</option>
                <option value="5">Lexus</option>
                <option value="6">Tesla</option>
            </select>
            </div>
            <br>

            <table id="myTable" class=" table order-list" style="width=100%">
                <thead >
                    <tr>
                        <td style="background-color:#e6e6e6">Point Discussed</td>
                        <td style="background-color:#e6e6e6">Remark</td>
                        <td style="background-color:#e6e6e6"></td>
                        <td style="background-color:#e6e6e6"></td>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="col-1" style="font-size:16px"> 1 
                            <span id="rate-1-1" onclick="gfg(1,this.id)" class="star">★</span> <span id="rate-1-2" onclick="gfg(2,this.id)" class="star">★</span><span id="rate-1-3" onclick="gfg(3,this.id)" class="star">★</span></td>
                        </td>
                        <td class="col-12">
                            <input type="text" name="remark"  class="form-control"/>
                            <input type="hidden" id="txtRate1" name="txtRate1" value="0">
                        </td>
                        <td class="col-sm-1"><a class="deleteRow"></a>
                            <input type="hidden" id="txtPIC1" name="txtPIC1" value="0">
                        </td>
                        <td class="col-sm-1"> 
                            <input type="button" data-toggle="modal" data-target="actionPlan" 
                                class="btn btn-primary a-btn-slide-text" value="Action Plan"/>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right;">
                            <input type="button" class="btn btn-primary" id="addrow" value="Add Row" />
                        </td>
                    </tr>
                    <tr>
                    </tr>
                </tfoot>
            </table>
            
            <br>
            <button type="submit" class="btn btn-primary">Create Post</button>
        </form>
    </div>
</div>

<!-- Modal Rejected-->
<div class="modal fade" id="actionPlan" role="dialog">
        <div class="modal-dialog"> 
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Action Plan</h4>
                </div>
                <div class="modal-body">
                    <p><label name="lineNumber" id="lineNumber" hidden></label></p>
                    <p>File Upload<input id="file" name="file" type="file" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"/></p>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-info" value="Upload" onclick="payInvoice()">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>   
        </div>
    </div>

<script>
var stars =  document.getElementsByClassName("star");
    $(document).ready(function () {
        var counter = 0;
        $("#addrow").on("click", function () {
            var newRow = $("<tr>");
            var cols = "";
            var no = counter +2;
            cols += '<td class="col-1">' +no +'<span id="rate-'+no+'-1" onclick="gfg(1,this.id)" class="star">★</span> <span id="rate-'+no+'-2" onclick="gfg(2,this.id)" class="star">★</span><span id="rate-'+no+'-3" onclick="gfg(3,this.id)" class="star">★</span></td>';
            cols += '<td class="col-12"><input type="text" class="form-control" name="remark' + counter + '"/><input type="hidden" id="txtRate'+no+'" name="txtRate'+no+'" value="0"></td>';

            cols += '<td class="col-sm-1"><button class="ibtnDel btn btn-md btn-danger"><i class="fa fa-trash"></i></button></td>';
            newRow.append(cols);
            $("table.order-list").append(newRow);
            counter++;
        });

        $("table.order-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            counter -= 1
        });
    });



    function calculateRow(row) {
        var price = +row.find('input[name^="price"]').val();

    }

    function calculateGrandTotal() {
        var grandTotal = 0;
        $("table.order-list").find('input[name^="price"]').each(function () {
            grandTotal += +$(this).val();
        });
        $("#grandtotal").text(grandTotal.toFixed(2));
    }

    
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
        document.getElementById("txtRate"+id.split("-")[1]).value = n;
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

        var minutes = Math.floor(res % 3600 / 60);

        var seconds = res % 60;
        document.getElementById("timeDifference").innerHTML = hours + ":" + minutes;

        // var duration = diff(document.getElementById("startTime").value, document.getElementById("endTime").value);
        // console.log(duration);
        // document.getElementById("duration").value =  duration ;
    }

    function removeColon( s)
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


    function initDialog(invoiceNo)
    {
        document.getElementById("invoiceNo").innerHTML = invoiceNo;
    }
</script>
@endsection