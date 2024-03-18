@foreach($users as $row)
<tr>
    <td>{{ $row->name}}</td>
    <td>{{ $row->email }}</td>
    <td>{{ $row->devision_id }}</td>
    <td>{{ App\Models\User::getStatusName($row->status)}}</td>
    <td align="center">
        <a href="{{ route('user/update', $row->id) }}"><span class="btn btn-success"><i class="bi bi-pencil-fill"></i></span></a>
        <a href="javascript:deleteUser('{{$row->id}}','{{$row->calendar_id}}')"> <span class="btn btn-danger"><i class="bi bi-trash3-fill"></i></span></a>
    </td>
</tr>
@endforeach
<tr>
    <td colspan="4" align="center">
    {!! $users->appends(Request::except('page'))->onEachSide(2)->links('meeting.pagination.custom') !!}
    </td>
</tr>
<script>

    function deleteUser(id)
    {
        let text = "Do you want to delete ?";
        if (confirm(text) == true) {
            $.ajax({
                type: 'post',
                data: {
                    id : id,
                },
                url: "{{ url('user/delete') }}",
                success: function(response) {
                    alert(response.message);
                    window.location = "{{ url('user')  }}";
                }

            })
        }
    }
</script>