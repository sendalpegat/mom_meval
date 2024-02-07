@extends('master')

@section('content')
<div>
    <div class="card-header-rounded">
        <h4>List User</h4>
    </div>
    <br>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-4">
        </div>
        <div class="col-md-8" style="text-align: right;">
        <form action="{{route('user')}}" method="GET">
            Name : <input type="text" name="seachTerm" value="{{ old('seachTerm') }}">
            Department : <select id="seachDeparment" name="seachDeparment">
            <?php 
                $oldDepartment = old('seachDeparment');
                echo '<option value="">All Department</option>';
                for ($i = 0; $i < count($data["departmentIds"]); $i++)
                {
                    $deptId = $data["departmentIds"][$i]->devision_id;
                    echo '<option value="'.$deptId.'">'.$deptId.'</option>';
                }
                ?>
            </select>
            <input type="submit" value="Search">
        </form>
        </div>
    </div>
    @csrf
    <?php $users = $data["users"]?>
    <div class="table-responsive">
    <table class="table table-striped table-bordered">
     <thead>
      <tr>
       <th width="40%"> Name @sortablelink('name',new \Illuminate\Support\HtmlString('&#8645;'))</th>
       <th width="30%">Email @sortablelink('email',new \Illuminate\Support\HtmlString('&#8645;'))</th>
       <th width="30%">Department <span id="post_title_icon"></span></th>
      </tr>
     </thead>
     <tbody>
      @include('user.pagination.pagination_child_user')
     </tbody>
    </table>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
</div>
<script>
    var oldDepartment = "<?php echo $oldDepartment; ?>";
    document.getElementById('seachDeparment').value = oldDepartment;
</script>
@endsection