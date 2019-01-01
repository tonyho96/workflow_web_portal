@extends('adminlte::page')

{{--@section('title', 'AdminLTE')--}}

@section('content')
    <!-- content -->
    @if(Session::has('message'))
        <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible">{{ Session::get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        </div>
    @endif
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box  box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create User</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        {!! Form::open(['action' => ['UserController@store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
                        {!! Form::token() !!}
                        <div class="box-body">
                            <div class="form-group required">
                                {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {!! Form::text('name', null, ['class' => 'form-control','required' =>'required']) !!}
                                </div>
                            </div>
                            <div class="form-group required">
                                {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {!! Form::email('email', null, ['class' => 'form-control','required' =>'required']) !!}
                                </div>
                            </div>
                            <div class="form-group required">
                                {!! Form::label('role', 'Role', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {!! Form::select('role', array('1' => 'CA', '2' => 'CR'),'', ['class' => 'form-control','required' =>'required']) !!}
                                </div>
                            </div>
                            <div class="form-group required">
                                {!! Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {!! Form::password('password', ['class' => 'form-control','required' =>'required','minlength'=>6]) !!}
                                </div>
                            </div>
                            @php
                            $teams = [
                                config('user.team.ATC') => 'ATC',
                                config('user.team.SHS') => 'SHS'
                            ];
                            @endphp
                            <div class="form-group required">
                                {!! Form::label('team', 'Team', ['class' => 'col-sm-2 control-label']) !!}
                                <div class="col-sm-10">
                                    {!! Form::select('team', $teams, null, ['class' => 'form-control','required' =>'required']) !!}
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{action('UserController@index')}}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right">Submit</button>
                        </div>
                        <!-- /.box-footer -->
                        {!! Form::close() !!}
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
    </div>
    </div>
@endsection