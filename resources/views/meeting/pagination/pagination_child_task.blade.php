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
    <td>{{ $row->remark}} <?php echo $row->note?> </td>
    <td>{{ $row->name }}</td>
    <td><?php echo date_format($row->due_date,"d M Y")?></td>
    <td>{{ App\Models\meeting\ActionPlan::getStatusName($row->status)}}</td>
    <td>
        @if ($row->status == App\Models\meeting\ActionPlan::STATUS_ON_PROGRESS)
        <a href="javascript:updateStatusTask('{{$row->mom_id}}','{{$row->point_discussed_index}}','{{$row->line_number}}')"> <span class="btn btn-primary">Update Status</span></a>
        @endif
    </td>
</tr>
@endforeach
<tr>
    <td colspan="4" align="center">
        {!! $tasks->appends(Request::except('page'))->links('meeting.pagination.custom') !!}
    </td>
</tr>
<script>

    function updateStatusTask(idMeeting, index, lineNumber)
    {
        let text = "Do you want to update status task to DONE ?";
        if (confirm(text) == true) {
            $.ajax({
                type: 'post',
                data: {
                    id : idMeeting,
                    index : index,
                    lineNumber : lineNumber
                },
                url: "{{ url('meeting/tasks/update') }}",
                success: function(response) {
                    alert(response.message);
                    window.location = "{{ url('meeting/tasks')  }}";
                }

            })
        }
    }
</script>