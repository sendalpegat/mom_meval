@extends('master')
@section('content')
<div>
    <div class="card-header-rounded">
        <h4>Profile</h4>
    </div>
    <div class="card-content-rounded" >
    <?php
        $id = "";
        $name = "";
        $email = "";
        $devisionId = "";
        $status = App\Models\User::ACTIVE;
        if (isset($data["user"]))
        {
            $id = $data["user"]->id;
            $name = $data["user"]->name;
            $email = $data["user"]->email;
            $devisionId = $data["user"]->devision_id;
            $status = $data["user"]->status;
        }

    ?>
    <div class="row">
        <div class="col-1">
            <div class="form-group" style="margin-left:10px;">
                <label for="title"><b>Name</b></label>
            </div>
        </div>
        <div class="col-11">
            <div class="form-group">
                <input type="hidden" id="txtId" value=" <?php echo $id; ?>"/>
                <input type="text" class="form-control" id="txtName" name="txtName" value="<?php echo $name; ?>">
            </div>
        </div>
        <div class="w-100"></div>
        <div class="col-1" style="margin-top:10px;">
            <div class="form-group" style="margin-left:10px;">
                <label for="title"><b>Email</b></label>
            </div>
        </div>
        <div class="col-11" style="margin-top:10px;">
            <div class="form-group">
                <input type="text" class="form-control" id="txtEmail" name="txtEmail" value="<?php echo $email; ?>">
            </div>
        </div>
        <div class="w-100"></div>
        <div class="col-1" style="margin-top:10px;">
            <div class="form-group" style="margin-left:10px;">
                <label for="title"><b>Devision</b></label>
            </div>
        </div>
        <div class="col-11" style="margin-top:10px;">
            <div class="form-group">
                <input type="text" disabled class="form-control" id="txtDevisionId" name="txtDevisionId" value="<?php echo $devisionId; ?>">
            </div>
        </div>

        @if (Auth::user()->role == App\Models\User::ADMIN)
        <div class="w-100"></div>
        <div class="col-1" style="margin-top:10px;">
            <div class="form-group" style="margin-left:10px;">
                <label for="title"><b>Status</b></label>
            </div>
        </div>
        <div class="col-11" style="margin-top:10px;">
            <div class="form-group">
                <select class="form-control" id="status" name="status" >
                        <option value="{{ App\Models\User::ACTIVE}}" <?php if($status == App\Models\User::ACTIVE) {echo "selected"; } ?> >{{App\Models\User::getStatusName(App\Models\User::ACTIVE)}}</option>
                        <option value="{{ App\Models\User::INACTIVE}}" <?php if($status == App\Models\User::INACTIVE){echo "selected";}?> >{{App\Models\User::getStatusName(App\Models\User::INACTIVE)}}</option>
                </select>
            </div>
        </div>
        @else
        <input type="hidden" id="status" value=" <?php echo $status; ?>"/>
        @endif
        <div class="w-100" style="margin-top:10px; text-align: right;">
            <button type="button" class="btn btn-primary" onclick="updateProfile()">Update</button>
        </div>
        <?php if (Auth::user()->id == $id || Auth::user()->role == App\Models\User::ADMIN) { ?>
        
        <div class="w-100">
            <br><br>
            <div class="card-content-header-rounded">
                <h6>Change Password</h6>
            </div>
        </div>
        <div class="w-100">
        <div class="card-content-rounded" >
            <div class="row">
                <?php if (Auth::user()->id == $id){?>
                <div class="col-2">
                    <div class="form-group" style="margin-left:10px;">
                        <label for="title"><b>Old Password</b></label>
                    </div>
                </div>
                <div class="col-10">
                    <div class="form-group">
                        <div class="input-group" id="show_hide_password">
                            <input class="form-control" type="password" id="txtOldPassword" name="txtOldPassword">
                            <div class="input-group-text">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>  
                        </div>
                    </div>
                </div> 
                <?php } ?>
                <div class="col-2" style="margin-top:10px;">
                    <div class="form-group" style="margin-left:10px;">
                        <label for="title"><b>New Password</b></label>
                    </div>
                </div>
                <div class="col-10" style="margin-top:10px;">
                    <div class="form-group">
                         <div class="input-group" id="show_hide_password">
                            <input class="form-control" type="password" id="txtNewPassword" name="txtNewPassword">
                            <div class="input-group-text">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>  
                        </div>
                    </div>
                </div> 
                <div class="col-2" style="margin-top:10px;">
                    <div class="form-group" style="margin-left:10px;">
                        <label for="title"><b>Confirm Password</b></label>
                    </div>
                </div>
                <div class="col-10" style="margin-top:10px;">
                    <div class="form-group">
                        <div class="input-group" id="show_hide_password">
                            <input class="form-control" type="password" id="txtConfirmPassword" name="txtConfirmPassword">
                            <div class="input-group-text">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>  
                        </div>
                    </div>
                    
                </div> 
               
                <div class="w-100" style="margin-top:10px; text-align: right;">
                <?php if (Auth::user()->id == $id){?>
                    <button type="button" class="btn btn-primary"  onclick="changePassword()">Change Password</button>
                <?php } else { ?>
                    <button type="button" class="btn btn-primary"  onclick="resetPassword()">Change Password</button>
                <?php } ?>
                </div>
            </div>
        </div>
        </div>    
        <?php } ?>
    </div>
</div>
<script>

    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if($('#show_hide_password input').attr("type") == "text"){
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass( "fa-eye-slash" );
                $('#show_hide_password i').removeClass( "fa-eye" );
            }else if($('#show_hide_password input').attr("type") == "password"){
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass( "fa-eye-slash" );
                $('#show_hide_password i').addClass( "fa-eye" );
            }
        });
    });

    function updateProfile()
    {
        let userId = document.getElementById('txtId').value;
        let name = document.getElementById('txtName').value;
        let email = document.getElementById('txtEmail').value;
        let status = document.getElementById('status').value;

        $.ajax({
                type: 'post',
                data: {
                    userId:userId,
                    name : name,
                    email : email,
                    status : status
                },
                url: "{{ url('user/edit') }}",
                success: function(response) 
                {
                    alert(response.message);
                    if (response.success)
                    {
                        window.location = "{{ url('user')  }}";
                    }
                }
            })
    }

    function changePassword()
    {
        let oldPassword = document.getElementById('txtOldPassword').value;
        let newPassword = document.getElementById('txtNewPassword').value;
        let confirmPassword = document.getElementById('txtConfirmPassword').value;

        if (newPassword == confirmPassword)
        {
            $.ajax({
                type: 'post',
                data: {
                    oldPassword : oldPassword,
                    newPassword : newPassword,
                },
                url: "{{ url('user/changePassword') }}",
                success: function(response) 
                {
                    alert(response.message);
                    if (response.success)
                    {
                        window.location = "{{ url('meeting')  }}";
                    }
                }
            })
        }
        else
        {
            alert("Confirm password not match");
        }
       
    }

    function resetPassword()
    {
        let newPassword = document.getElementById('txtNewPassword').value;
        let id = document.getElementById('txtId').value;

        $.ajax({
            type: 'post',
            data:{
                id : id,
                newPassword : newPassword,
            },
            url: "{{ url('user/resetPassword') }}",
            success: function(response) 
            {
                alert(response.message);
                if (response.success)
                {
                    window.location = "{{ url('user')  }}";
                }
            }
        })
    }
</script>
@endsection