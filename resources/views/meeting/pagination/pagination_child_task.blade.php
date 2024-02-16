@foreach($tasks as $row)
<tr>
    <td>
            @if ($row->rate == 3)
                <span class="star three">★</span><span class="star three">★</span><span class="star three">★</span>
            @elseif ($row->rate == 2)
                <span class="star two">★</span><span class="star two">★</span><span class="star">★</span>
            @elseif ($row->rate == 1)
                <span class="star one">★</span><span class="star">★</span><span class="star">★</span>
            @else
                <span class="star">★</span><span class="star">★</span><span class="star">★</span>
            @endif
    </td>
    <td>{{ $row->remark_point}} <?php echo $row->note?> </td>
    <td>{{ $row->name }}</td>
    <td><?php echo date_format($row->due_date,"d M Y")?></td>
    <td>{{ App\Models\meeting\ActionPlan::getStatusName($row->status)}}
        @if ($row->status == App\Models\meeting\ActionPlan::STATUS_DONE)
        <?php echo $row->remark; ?>
        @endif
    </td>
    <td>
        @if ($row->status == App\Models\meeting\ActionPlan::STATUS_ON_PROGRESS)
            <button type="button" class="btn btn-primary" id="myBtn" data-bs-toggle="modal" data-bs-target="#myModal" onclick="initDialog('{{$row->mom_id}}','{{$row->point_discussed_index}}','{{$row->line_number}}')">Update Status</button>
        @endif
    </td>
</tr>
@endforeach
<tr>
    <td colspan="6" align="center">
        {!! $tasks->appends(Request::except('page'))->onEachSide(2)->links('meeting.pagination.custom') !!}
    </td>
</tr>


<div class="modal fade" id="myModal" role="dialog" data-bs-focus="false" >
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Task</h5>
        </div>
        <div class="modal-body" >
            <div>
                <input type="hidden" id="txtIdMeeting" name="txtIdMeeting" value="0">
                <input type="hidden" id="txtIndex" name="txtIndex" value="0">
                <input type="hidden" id="txtLineNumber" name="txtLineNumber" value="0">
            </div>
            <div><b>Note :</b></div>
            <textarea id="notes" rows="6" cols="150" class="form-control" value=""></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-success" type="submit" name="submit" value="Submit" onclick="updateStatusTask()">Save</button>
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
</div>

<script>
    //set area as text rich
    ClassicEditor.create(document.querySelector("#notes"))
        .then( newEditor => {
            notes = newEditor;
        } )
        .catch(error => {
            console.error( error );
        });

    function initDialog(idMeeting, index, lineNumber)
    {
        document.getElementById('txtIdMeeting').value = idMeeting;
        document.getElementById('txtIndex').value = index;
        document.getElementById('txtLineNumber').value = lineNumber;
    }

    function updateStatusTask()
    {
        let idMeeting = document.getElementById('txtIdMeeting').value;
        let index = document.getElementById('txtIndex').value;
        let lineNumber = document.getElementById('txtLineNumber').value;
        var note = notes.getData();
        $.ajax({
                type: 'post',
                data: {
                    id : idMeeting,
                    index : index,
                    lineNumber : lineNumber,
                    note : note
                },
                url: "{{ url('meeting/tasks/update') }}",
                success: function(response) {
                    alert(response.message);
                    window.location = "{{ url('meeting/tasks')  }}";
                }

            });
    }
</script>