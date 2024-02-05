@foreach($users as $row)
<tr>
    <td>{{ $row->name}}</td>
    <td>{{ $row->email }}</td>
    <td>{{ $row->devision_id }}</td>
</tr>
@endforeach
<tr>
    <td colspan="5" align="center">
    {!! $users->appends(Request::except('page'))->links('meeting.pagination.custom') !!}
    </td>
</tr>
