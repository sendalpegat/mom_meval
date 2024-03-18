@extends('master')

@section('content')
<div>
    <div class="card-header-rounded">
        <h4>List User</h4>
    </div>
    <br>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-2">
        </div>
        <div class="col-md-10" style="text-align: right;">
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
            Status : 
                <?php $oldStatus = App\Models\User::ACTIVE;
                      if (old('seachStatus') != "")
                        $oldStatus = old('seachStatus');
                ?>
                <select id="seachStatus" name="seachStatus" >
                        <option value="-1" >All</option>
                        <option value="{{ App\Models\User::ACTIVE}}">{{App\Models\User::getStatusName(App\Models\User::ACTIVE)}}</option>
                        <option value="{{ App\Models\User::INACTIVE}}">{{App\Models\User::getStatusName(App\Models\User::INACTIVE)}}</option>
                </select>
            <button class="btn btn-primary" type="submit">Search <i class="fa fa-search"></i></button>
        </form>
        </div>
    </div>
    @csrf
    <?php $users = $data["users"]?>
    <div class="table-responsive">
    <table class="table table-striped table-bordered">
     <thead>
      <tr>
       <th width="30%"> Name @sortablelink('name',new \Illuminate\Support\HtmlString('&#8645;'))</th>
       <th width="25%">Email @sortablelink('email',new \Illuminate\Support\HtmlString('&#8645;'))</th>
       <th width="25%">Department</th>
       <th width="10%">Status</th>
       <th width="10%">Action</th>
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
    var oldStatus = "<?php echo $oldStatus; ?>";
    document.getElementById('seachDeparment').value = oldDepartment;
    document.getElementById('seachStatus').value = oldStatus;
</script>
@endsection