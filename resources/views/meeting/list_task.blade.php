@extends('master')

@section('content')
<div>
    <div class="card-header-rounded">
        <h4>List Action Plan</h4>
    </div>
    <br>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-8">
        </div>
        <div class="col-md-4" style="text-align: right;">
        <form action="{{route('meeting/tasks')}}" method="GET">
            <input type="text" name="seachTerm" placeholder="Search task by remark .." value="{{ old('seachTerm') }}">
            <input type="submit" value="Search">
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
@endsection