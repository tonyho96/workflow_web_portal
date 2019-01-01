@extends('adminlte::page')

{{--@section('title', 'AdminLTE')--}}

@section('content_header')
    <h1 style="background-color: #ffffff; margin: -15px -15px 0px -15px; padding: 15px 20px;"><i class="fa fa-user"
                                                                                                 aria-hidden="true"></i>&nbsp;Change
        Password</h1>
@stop

@section('content')
    @if ($errors->has('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{$errors->first('error')}}
        </div>
    @endif
    @if (Session::has('message'))

        @if(Session::has('message'))
            <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible">{{ Session::get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            </div>
        @endif
    @endif
    <div class="container-fluid" style="background-color: #ffffff; padding: 15px; border: 1px solid #ddd;">
        <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button type="submit" class="btn btn-primary" id="submit-password-btn" disabled>
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                        Save
                    </button>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="old_pass" class="col-sm-2 control-label">Current Password: </label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="old_password" name="old_password" required/>
                            @if ($errors->has('old_password'))
                                <span><font color="red">{{ $errors->first('old_password') }}</font></span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="new_password" class="col-sm-2 control-label">New Password:</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="new_password" name="new_password" required
                                   minlength="6"/>
                            @if ($errors->has('new_password'))
                                <span><font color="red">{{ $errors->first('new_password') }}</font></span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_pass" class="col-sm-2 control-label">Confirm New Password:</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required
                                   minlength="6"/>
                            @if ($errors->has('confirm_pass'))
                                <span><font color="red">{{ $errors->first('confirm_pass') }}</font></span>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop
@push('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#old_pass, #new_password, #confirm_pass').on('input', function () {
                if ($('#new_password').val() == $('#confirm_pass').val() && $('#old_pass').val() != '' && $('#new_password').val() != '' && $('#confirm_pass').val() != '') {
                    $('#submit-password-btn').removeAttr("disabled");
                    $('#message').html('');
                } else if ($('#old_pass').val() == '') {
                    $('#submit-password-btn').prop('disabled', true);
                    $('#message').html('Current Password is empty').css('color', 'red');
                } else if ($('#new_password').val() != '' && $('#confirm_pass').val() != '') {
                    $('#submit-password-btn').prop('disabled', true);
                    $('#message').html('Password Not Matching').css('color', 'red');
                }
            });
        });
    </script>
@endpush
