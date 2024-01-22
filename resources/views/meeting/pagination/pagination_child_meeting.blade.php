@foreach($meetings as $row)
<tr>
    <td>{{ $row->topic}}</td>
    <td>{{ $row->location }}</td>
    <td>{{ $row->mom_date }}</td>
    <td>
        <a href="{{ route('meeting/update', $row->mom_id) }}"> <span class="bi bi-pencil-fill"></span></a>
        <a href="javascript:deleteMeeting('{{$row->mom_id}}')"> <span class="bi bi-trash3-fill"></span></a>
    </td>
</tr>
@endforeach
<tr>
    <td colspan="4" align="center">
        {!! $meetings->appends(Request::except('page'))->links('meeting.pagination.custom') !!}
    </td>
</tr>

<script>

    function deleteMeeting(idMeeting)
    {
        let text = "Do you want to delete ?";
        if (confirm(text) == true) {
            $.ajax({
                type: 'post',
                data: {
                    id : idMeeting
                },
                url: "{{ url('meeting/delete') }}",
                success: function(response) {
                    alert(response.message);
                    window.location = "{{ url('meeting')  }}";
                }

            })
        }
    }
</script>
