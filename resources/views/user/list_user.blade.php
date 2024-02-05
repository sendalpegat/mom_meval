@extends('master')

@section('content')
<div>
    <div class="card-header-rounded">
        <h4>List User</h4>
    </div>
    <br>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-md-8">
        </div>
        <div class="col-md-4" style="text-align: right;">
        <form action="{{route('user')}}" method="GET">
            <input type="text" name="seachTerm" placeholder="Search user by name.." value="{{ old('seachTerm') }}">
            <input type="submit" value="Search">
        </form>
        </div>
    </div>
    @csrf
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
@endsection