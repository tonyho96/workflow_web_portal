@extends('adminlte::page')

{{--@section('title', 'AdminLTE')--}}

@section('content')
    <!-- content -->
    <section class="content">
        @if ($errors->has('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{$errors->first('error')}}
            </div>
        @endif
        @if (Session::has('message'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <p><i class="icon fa fa-check"></i>{{Session::get('message')}}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">User list</h3><br>
                        <a href="{{ action('AvailableCaUserController@create') }}" class="btn btn-success"><span
                                    class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="users-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    @if ($user->role === 1)
                                        <td>CA</td>
                                    @else
                                        <td>CR</td>
                                    @endif
                                    <td>
                                        <a href="{{ action('UserController@edit', $user->id) }}"
                                           class="btn btn-success"><span class="glyphicon glyphicon-pencil"
                                                                         aria-hidden="true"></span></a>
                                        @if ($user->email !== Auth::user()->email)
                                            {!! Form::open(['action' => ['UserController@destroy', $user->id],
                                                'method' => 'DELETE',
                                                'onsubmit' => 'return confirmForm(this);',
                                                'data-confirm-message' => trans('labels.confirm_delete'),
                                                'class' => 'inline',
                                                ]) !!}

                                            {{ Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger', 'onClick' => 'return window.confirm("Are you sure?")']) }}
                                            {!! Form::close() !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
    </div>
    </div>
@endsection


{{--@push('scripts')--}}
{{--<script>--}}
{{--$(function() {--}}
{{--$('#users-table').DataTable({--}}
{{--processing: true,--}}
{{--serverSide: true,--}}
{{--ajax: '{{action('UserController@dataTable')}}',--}}
{{--columns: [--}}
{{--{ data: 'name', name: 'name' },--}}
{{--{ data: 'name', name: 'name' },--}}
{{--{ data: 'email', name: 'email'},--}}
{{--{ data: 'role', name: 'role' },--}}
{{--//                { data: 'action', name: 'action', orderable: false, searchable: false }--}}
{{--]--}}
{{--});--}}
{{--});--}}
{{--</script>--}}
{{--@endpush--}}