@extends('master')
@section('content')
<div>
    <div class="card-header-rounded">
        <h4>Dashboard</h4>
    </div>
    <br>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-3"></div>
        <div class="col-md-3"><div class="rectangle-dashboard" >Task Complete : <b><?php echo $data["task"][App\Models\meeting\ActionPlan::STATUS_DONE] ?></b> </div></div>
        <div class="col-md-3"><div class="rectangle-dashboard" >Task Uncomplete : <b><?php echo $data["task"][App\Models\meeting\ActionPlan::STATUS_ON_PROGRESS] ?></b> </div></div>
        <div class="col-md-3"></div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-2">
        </div>
        <div class="col-md-12" style="text-align: right;">
        <form action="{{route('meeting')}}" method="GET">
            Topic : <input type="text" name="seachTerm" value="{{ old('seachTerm') }}"> 
            <?php $oldDepartment = ""; ?>
            @if (Auth::user()->role == App\Models\User::ADMIN)
                Department : 
                <select id="seachDeparment" name="seachDeparment" >
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
            @endif

            <?php 
            $oldUser = old('seachUser');
            if (!empty($data["users"])) {
                if (count($data["users"]) > 1) {
            ?>
                User : 
                <select id="seachUser" name="seachUser">
                <?php 
                    echo '<option value="">All User</option>';
                    for ($i = 0; $i < count($data["users"]); $i++)
                    {
                        echo '<option value="'.$data["users"][$i]->email.'">'.$data["users"][$i]->name.'</option>';
                    }
                    ?>
                </select>
            <?php 
                }
            } 
            ?>
            <input type="submit" value="Search">
        </form>
        </div>
    </div>
    @csrf
    <?php $meetings = $data["meetings"]?>
    <div class="table-responsive">
    <table class="table table-striped table-bordered">
     <thead>
      <tr>
       <th width="22%" >Topic @sortablelink('topic',new \Illuminate\Support\HtmlString('&#8645;'))</th>
       <th width="20%" >Location</th>
       <th width="14%">Created by @sortablelink('created_at',new \Illuminate\Support\HtmlString('&#8645;'))</th>
       <th width="14%">Upated by </th>
       <th width="15%">Department</th>
       <th width="10%">Status</th>
       <th width="5%">Action</th>
      </tr>
     </thead>
     <tbody>
      @include('meeting.pagination.pagination_child_meeting')
     </tbody>
    </table>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
</div>
<script>
    var oldDepartment = "<?php echo $oldDepartment; ?>";
    var oldUser = "<?php echo $oldUser; ?>";
    document.getElementById('seachDeparment').value = oldDepartment;
    document.getElementById('seachUser').value = oldUser;
    $(document).ready(function() {
        $('#seachUser').select2();
        $('#seachDeparment').select2();
    });
</script>
@endsection