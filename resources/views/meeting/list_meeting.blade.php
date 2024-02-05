

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
        <div class="col-md-4">
        </div>
        <div class="col-md-8" style="text-align: right;">
        <form action="{{route('meeting')}}" method="GET">
            Topic : <input type="text" name="seachTerm" value="{{ old('seachTerm') }}"> Department : <input type="text" name="seachDeparment" value="{{ old('seachDeparment') }}">
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
       <th width="28%" >Topic @sortablelink('topic',new \Illuminate\Support\HtmlString('&#8645;'))</th>
       <th width="18%" >Location</th>
       <th width="12%">Date @sortablelink('created_at',new \Illuminate\Support\HtmlString('&#8645;'))</th>
       <th width="10%">Upated by</th>
       <th width="8%">Department</th>
       <th width="5%">Action</th>
      </tr>
     </thead>
     <tbody>
      @include('meeting.pagination.pagination_child_meeting')
     </tbody>
    </table>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
</div>
@endsection