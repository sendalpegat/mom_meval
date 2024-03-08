<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MOM - Meval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <link rel="stylesheet" href="{{ asset('/public/css/app.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('/public/css/jquery.timepicker.css') }}" type="text/css">
    
    <script src="{{ asset('/public/js/multiselect-dropdown.js') }}" ></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('/public/js/jquery.timepicker.js') }}" ></script>
</head>
<body>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
</script>

<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0" style="background-color:#eeeeee; ">
        <br>
            <div class="img-container" style="text-align:center">
                <img src=" {{ asset('/public/images/logo-dark.png') }}" alt="Image" width="210" height="65" >
            </div>
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                </a>
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                    <li class="nav-item">
                        <a href="{{route('meeting')}}" class="nav-link align-middle px-0 text-dark font-weight-bold">
                            <i class="bi bi-card-list fs-4"></i> <span class="ms-1 d-none d-sm-inline">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('meeting/create')}}" class="nav-link px-0 align-middle text-dark font-weight-bold">
                            <i class="bi bi-file-earmark-plus fs-4"></i> <span class="ms-1 d-none d-sm-inline">Create Meeting</span> 
						            </a>
                    </li>
                    <li>
                        <a href="{{route('meeting/tasks')}}" class="nav-link px-0 align-middle text-dark font-weight-bold">
                            <i class="fs-4 bi bi-journal-check"></i> <span class="ms-1 d-none d-sm-inline">Action Plan</span></a>
                    </li>
                    <li>
                        <a href="{{route('meeting/calendar')}}" class="nav-link px-0 align-middle text-dark font-weight-bold">
                            <i class="fs-4 bi bi-calendar-week"></i> <span class="ms-1 d-none d-sm-inline">Calendar</span></a>
                    </li>
                    <a href="{{route('user')}}" class="nav-link px-0 align-middle text-dark font-weight-bold">
                            <i class="fs-4 bi bi-person-fill"></i> <span class="ms-1 d-none d-sm-inline">List Users</span></a>
                    <li>
                    
                    </li>
                    @if (Auth::user()->role == App\Models\User::ADMIN)
                    <li>
                        <a href="javascript:syncUser()" class="nav-link px-0 align-middle text-dark font-weight-bold">
                            <i class="fs-4 bi bi-person-down"></i> <span class="ms-1 d-none d-sm-inline">Sync User Odoo</span></a>
                    </li>
                    @endif
                    
                </ul>
                <hr>
                <div class="dropdown pb-4">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src=" {{ asset('/public/images/account-icon.png') }}" alt="Image" width="30" height="30">
                        <span class="d-none d-sm-inline mx-1 text-dark font-weight-bold">{{Auth::user()->name}}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        
                        <li><a class="dropdown-item" href="{{route('actionlogout')}}">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col py-3">
        @yield('content')
        </div>
    </div>
</div>

<div class="modal" id="loadingModal" role="dialog" data-bs-focus="false" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Synchron user from Odoo</h5>
        </div>
        <div class="modal-body" style="text-align: center;">
            Please Wait
            <div class="spinner-grow spinner-grow-sm text-info"></div>
            <div class="spinner-grow spinner-grow-sm text-info"></div>
            <div class="spinner-grow spinner-grow-sm text-info"></div>
        </div>
        
      </div>
      
    </div>
  </div>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script> 
    function syncUser()
    {
        $('#loadingModal').modal('show');
        $.ajax({
            type: 'GET',
            url: "{{ url('user/sync-odoo') }}",
            success: function(response) {
                $('#loadingModal').modal('hide');
                alert(response.message);
            },
            error: function(response){
                $('#loadingModal').modal('hide');
                alert(response.message);
                
            }

        });

    }

</script>
</html>