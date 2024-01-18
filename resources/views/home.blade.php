@extends('master')

@section('content')
  <h4>Selamat Datang <b>{{Auth::user()->email}}</b>, Anda Login sebagai <b></b>.</h4>
@endsection