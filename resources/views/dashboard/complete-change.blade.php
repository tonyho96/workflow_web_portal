@extends('adminlte::page')
@section('content_header')
    <style type="text/css">
        #approve-reject {

        }

        table.dataTable thead th,
        table.dataTable thead td {
            padding: 2.5px 33px 2.5px 18px;
            border: 1px solid #dddddd;
            border-right: 0px;
            background-color: #487FBA;
            color: #ffffff;
            font-weight: normal;
            text-align: left;
            position: relative;
        }

        table.dataTable thead th:last-child,
        table.dataTable thead td:last-child {
            border-right: 1px solid #dddddd;
        }

        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc,
        table.dataTable thead .sorting {
            background-image: none;
        }

        table.dataTable thead .sorting_asc::before,
        table.dataTable thead .sorting_desc::before,
        table.dataTable thead .sorting::before {
            content: '';
            color: #000000;
            text-align: center;
            line-height: 100%;
            position: absolute;
            top: 0px;
            bottom: 0px;
            right: 0px;
            width: 15px;
            background-color: #F6F7FA;
            border: .5px solid #dddddd;
        }

        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::after,
        table.dataTable thead .sorting::after {
            content: '';
            color: #000000;
            font-family: FontAwesome;
            text-align: center;
            position: absolute;
            right: 0px;
            width: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        table.dataTable thead .sorting::after {
            content: '\f0dc';
        }

        table.dataTable thead .sorting_desc::after {
            content: '\f0dd';
        }

        table.dataTable thead .sorting_asc::after {
            content: '\f0de';
        }

        .dataTables_info {
            display: none !important;
        }

        table.dataTable tbody td {
            border-left: 1px solid #dddddd !important;
            border-bottom: 1px solid #dddddd !important;
            padding: 2.5px 18px;
        }

        table.dataTable tbody td:last-child {
            border-right: 1px solid #dddddd !important;
        }

        table.dataTable.no-footer {
            border-bottom: 0px;
        }
    </style>
    <h1 style="background-color: #ffffff; margin: -15px -15px 0px -15px; padding: 15px 20px; color: #5EA1D6;">Complete
        Change</h1>
@stop
@section('content')
    <div id="complete_change" style="background-color: #ffffff; min-height: 100vh; margin: -15px;">
        <br/>
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
        @if ($errors->has('completion_notes'))
            <span class="help-block">
		<strong>{{ $errors->first('completion_notes') }}</strong>
	</span>
        @endif
        @if ($errors->has('completion_signature'))
            <span class="help-block">
		<strong>{{ $errors->first('completion_signature') }}</strong>
	</span>
        @endif
        @if ($errors->has('complete_status'))
            <span class="help-block">
		<strong>{{ $errors->first('complete_status') }}</strong>
	</span>
        @endif
        <div class="table-responsive" style="padding: 15px;">
            <table class="datatables">
                <thead>
                <tr>
                    <th style="display:none;">id</th>
                    <th>Reference</th>
                    <th>Title</th>
                    <th>Raised By</th>
                    <th>Raised Date</th>
                    <th>Approved By</th>
                    <th>Approved Date</th>
                    <th>Planned Date</th>
                    <th>Implemented</th>
                    <th>Status</th>
                    <th>Change Type</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach( $forms as $form )
                    <?php
                    //$user_raised = DB::table('users')->where('id', '=', $form->raised_by_user_id)->first();
                    $user_raised = $form->raisedBy;
                    $approved_rejected_by = '';
                    if (!empty($form->approved_rejected_by)) {
                        $user_approved_rejected = DB::table('users')->where('id', '=', $form->approved_rejected_by)->first();
                        $approved_rejected_by = $user_approved_rejected->name;
                    }
                    ?>
                    <tr>
                        <td style="display:none;">
                            {{ $form->id }}
                        </td>
                        <td>{{ $form->reference }}</td>
                        <td>{{ $form->change_title }}</td>
                        <td>{{ $user_raised->name  }}</td>
                        <td>{{ $form->date_raised }}</td>
                        <td>{{ $approved_rejected_by }}</td>
                        <td>{{ $form->approved_rejected_date }}</td>
                        <td>{{ "$form->change_start_date $form->change_start_time - $form->change_end_date $form->change_end_time" }}</td>
                        <td>{{ $form->complete_date }}</td>
                        <td>
                            @if ( $form->status == config('form.status.approved') )
                                Approved
                            @elseif ( $form->status == config('form.status.rejected') )
                                Rejected
                            @elseif ( $form->status == config('form.status.pending_CAB') )
                                Pending CAB
                            @endif
                        </td>
                        <td>
                            @if($form->change_type== config('form.change_type.normal'))
                                Normal
                            @elseif($form->change_type== config('form.change_type.self_approve'))
                                Self Approved
                            @elseif($form->change_type== config('form.change_type.emergency'))
                                Emergency
							@elseif($form->change_type== config('form.change_type.bypass'))
                                By Pass
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-info" type="button" name="complete" id="complete" value="Complete"
                                    data-toggle="modal" data-target="#myModal">Complete
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Complete Change</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">


                        <div class="box box-default">
                            {!! Form::open(['action' => ['FormController@storeCompleteChangeForm'], 'method' => 'POST', 'id' => 'save-form']) !!}
                            {{ Form::hidden('form_id', '', array('id' => 'form_id')) }}
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {!! Form::label('complete_date', 'Completion Date', ['class' => '']) !!}
                                            {!! Form::text('complete_date', date('Y-m-d'),['class' => 'form-control  date datetime','required' =>'required', 'style'=>'width:500px']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4  {{ $errors->has('change_reference') ? 'has-error' : '' }}">
                                        <div class="form-group">
                                            {!! Form::label('completion_notes', 'Completion Notes', ['class' => '']) !!}
                                            {!! Form::textarea('completion_notes', null,['class' => 'form-control','required' =>'required', 'style'=>'width:500px']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 {{ $errors->has('raised_by_user_id') ? 'has-error' : '' }}">
                                        <div class="form-group">
                                            {!! Form::label('completion_signature', 'Completion Signature', ['class' => '']) !!}
                                            {!! Form::text('completion_signature', null,['class' => 'form-control','required' =>'required', 'style'=>'width:500px']) !!}
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $complete_status_array=array(config('form.complete_status.success')=>"success",config( 'form.complete_status.unsuccess')=>"unsuccess");
                                @endphp
                                <div class="row">
                                    <div class="col-sm-4 {{ $errors->has('raised_by_user_id') ? 'has-error' : '' }}">
                                        <div class="form-group">
                                            {!! Form::label('complete_status', 'Complete Status', ['class' => '']) !!}
                                            {!! Form::select('complete_status',$complete_status_array, null, ['class' => 'form-control','required' =>'required', 'style'=>'width:500px']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="save" name="save" type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        @stop

        @section('js')
            <script type="text/javascript">
                $(document).ready(function () {
                    var table = $('.datatables').DataTable({
                        searching: false,
                        paging: false,
                        columnDefs: [{
                            "targets": 10, "orderable": false
                        }]
                    });
                    $('.datatables tbody').on('click', 'button', function () {
                        var data = table.row($(this).parents('tr')).data();
                        $("#form_id").val(data[0])

                        //alert(data[0])
                    });
                    $('#complete_date').datetimepicker({
                        minDate: false
                    });

                });
            </script>

@stop