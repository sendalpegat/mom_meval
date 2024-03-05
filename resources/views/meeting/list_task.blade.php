@extends('master')

@section('content')
<div>
    <div class="card-header-rounded">
        <h4>List Action Plan</h4>
    </div>
    <br>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6" style="text-align: right;">
        <form action="{{route('meeting/tasks')}}" method="GET">
        <?php $oldStatus = App\Models\meeting\ActionPlan::STATUS_ON_PROGRESS;
              if ( old('searchStatus') != "")
                $oldStatus = old('searchStatus');
                    
        ?>
            Status : <select id="searchStatus" name="searchStatus" >
                        <option value="-1" >All</option>
                        <option value="{{ App\Models\meeting\ActionPlan::STATUS_ON_PROGRESS}}" >{{App\Models\meeting\ActionPlan::getStatusName(App\Models\meeting\ActionPlan::STATUS_ON_PROGRESS)}}</option>
                        <option value="{{ App\Models\meeting\ActionPlan::STATUS_DONE}}">{{App\Models\meeting\ActionPlan::getStatusName(App\Models\meeting\ActionPlan::STATUS_DONE)}}</option>
                    </select>

            Remark : <input type="text" name="seachTerm" value="{{ old('seachTerm') }}">
            <button class="btn btn-primary" type="submit">Search <i class="fa fa-search"></i></button>
        </form>
        </div>
    </div>
    @csrf
    <div class="table-responsive">
    <table class="table table-striped table-bordered">
     <thead>
      <tr>
      <th width="5%">Priority</th>
       <th width="40%">Remark <span id="id_icon"></span></th>
       <th width="15%" class="sorting" >PIC <span id="post_title_icon"></span></th>
       <th width="10%">Date</th>
       <th width="25%">Status</th>
       <th width="5%">Action</th>
      </tr>
     </thead>
     <tbody>
      @include('meeting.pagination.pagination_child_task')
     </tbody>
    </table>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
</div>
<script>
    var oldStatus = "<?php echo $oldStatus; ?>";
    document.getElementById('searchStatus').value = oldStatus;
</script>
@endsection