<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MOM - Meval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}" type="text/css">
    
    <script src="{{ asset('/js/multiselect-dropdown.js') }}" ></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline">Menu</span>
                </a>
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                    <li class="nav-item">
                        <a href="{{route('meeting')}}" class="nav-link align-middle px-0">
                            <i class="bi bi-card-list fs-4"></i> <span class="ms-1 d-none d-sm-inline">List Meeting</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('meeting/create')}}" class="nav-link px-0 align-middle">
                            <i class="bi bi-file-earmark-plus fs-4"></i> <span class="ms-1 d-none d-sm-inline">Create Meeting</span> 
						            </a>
                    </li>
                    <li>
                        <a href="{{route('meeting/tasks')}}" class="nav-link px-0 align-middle">
                            <i class="fs-4 bi bi-journal-check"></i> <span class="ms-1 d-none d-sm-inline">Action Plan</span></a>
                    </li>
                    <li>
                        <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                            <i class="fs-4 bi bi-person-down"></i> <span class="ms-1 d-none d-sm-inline">Sync User Odoo</span></a>
                    </li>
                    
                </ul>
                <hr>
                <div class="dropdown pb-4">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src=" {{ asset('images/account-icon.png') }}" alt="Image" width="30" height="30">
                        <span class="d-none d-sm-inline mx-1">{{Auth::user()->name}}</span>
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

</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</html>