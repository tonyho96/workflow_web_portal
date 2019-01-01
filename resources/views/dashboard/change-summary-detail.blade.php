@extends('adminlte::page')
{{--@section('title', 'AdminLTE')--}}
@section('content_header')
    <h1 style="background-color: #ffffff; margin: -15px -15px 0px -15px; padding: 15px 20px;">Request for Change</h1>
@stop
@section('content')
    <style>
        h4 {
            font-weight: 700;
            font-size: 14px;
        }
    </style>
    <?php
    $userSelectData = [];
    foreach ($users as $user) {
        $userSelectData[$user->id] = $user->name;
    }

    $changeTypeSelect = [
        config('form.change_type.normal') => 'Normal',
        config('form.change_type.self_approve') => 'Minor/self approved',
        config('form.change_type.emergency') => 'Emergency',
	    config('form.change_type.bypass') => 'By Pass'
    ];

    $businessPrioritySelect = [
        config('form.business_priority.business_critical') => 'Business critical: no work-around',
        config('form.business_priority.high') => 'High: e.g. replace work-around',
        config('form.business_priority.medium_problem_issue_resolution') => 'Medium: problem/issue resolution',
        config('form.business_priority.medium_essential_enhancement') => 'Medium: essential enhancement',
        config('form.business_priority.low') => 'Low: non-essential or cosmetic'
    ];

    $status = [
        config('form.status.pending_CAB') => 'Pending CAB',
        config('form.status.approved') => 'Approved',
        config('form.status.rejected') => 'Rejected',
    ];

    $completeStatus = [
        config('form.complete_status.planned') => 'Planned',
        config('form.complete_status.waiting_for_complete') => 'Waiting for complete',
        config('form.complete_status.success') => 'Success',
        config('form.complete_status.unsuccess') => 'Unsuccess',
        
    ];

    ?>

    <div class="container-fluid">

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
        <div class="box box-default">
            {!! Form::open(['action' => ['DashboardController@editSummaryDetail',$form->reference], 'method' => 'POST', 'id' => 'create-form', 'enctype' => 'multipart/form-data']) !!}
            {!! Form::token() !!}

            <div class="box-body">
                <h4>Section 1 – Details of Request </h4>
                <div class="row">
                    <div class="col-sm-4 {{ $errors->has('raised_by_user_id') ? 'has-error' : '' }}">
                        <div class="form-group">
                            {!! Form::label('raised_by_user_id', 'Raised By', ['class' => '']) !!}
                            {!! Form::select('raised_by_user_id', $userSelectData, $form->raised_by_user_id, ['class' => 'form-control','required' =>'required']) !!}
                            @if ($errors->has('raised_by_user_id'))
                                <span class="help-block">
                                <strong>{{ $errors->first('raised_by_user_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-4 {{ $errors->has('contact_no') ? 'has-error' : '' }}">
                        <div class="form-group">
                            {!! Form::label('contact_no', 'Contact No', ['class' => '']) !!}
                            {!! Form::text('contact_no', $form->contact_no, ['class' => 'form-control','required' =>'required']) !!}

                            @if ($errors->has('contact_no'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('contact_no') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>


                    <div class="col-sm-4 ">
                        <div class="form-group">
                            {!! Form::label('smart_hub_impact', 'Smart Hub Impact', ['class' => '']) !!}

                            <div class="form-control" >
                                @if($form->smart_hub_impact == config('form.smart_hub_impact.yes'))
                                    <input type="radio" name="smart_hub_impact" required value="{{config('form.smart_hub_impact.yes')}}" checked> Yes
                                    <input type="radio" name="smart_hub_impact" value="{{config('form.smart_hub_impact.no')}}"> No
                                @else
                                    <input type="radio" name="smart_hub_impact" required value="{{config('form.smart_hub_impact.yes')}}" > Yes
                                    <input type="radio" name="smart_hub_impact" value="{{config('form.smart_hub_impact.no')}}" checked> No
                                @endif

                            </div>

                            @if ($errors->has('smart_hub_impact'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('smart_hub_impact') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>



                    <div class="col-sm-12 {{ $errors->has('change_type') ? 'has-error' : '' }}">
                        <div class="form-group">
                            {!! Form::label('change_type', 'Change Type', ['class' => '']) !!}
                            {!! Form::select('change_type', $changeTypeSelect, $form->change_type, ['class' => 'form-control','required' =>'required']) !!}

                            @if ($errors->has('change_type'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('change_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-12 {{ $errors->has('change_title') ? 'has-error' : '' }}">
                        <div class="form-group">
                            {!! Form::label('change_title', 'Change Title', ['class' => '']) !!}
                            {!! Form::text('change_title', $form->change_title, ['class' => 'form-control','required' =>'required']) !!}

                            @if ($errors->has('change_title'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('change_title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-12 {{ $errors->has('proposed_change_owner_user_id') ? 'has-error' : '' }}">
                        <div class="form-group">
                            {!! Form::label('proposed_change_owner_user_id', 'Proposed Change Owner', ['class' => 'control-label pull-left']) !!}
                            {!! Form::select('proposed_change_owner_user_id', $userSelectData, $form->proposed_change_owner_user_id, ['class' => 'form-control col-sm-9 pull-right', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}
                            @if ($errors->has('proposed_change_owner_user_id'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('proposed_change_owner_user_id') }}</strong>
                              </span>
                            @endif
                        </div>
                    </div>
                <!-- <div class="form-group {{ $errors->has('date_raised') ? 'has-error' : '' }}">
                        {!! Form::label('date_raised', 'Date Raised', ['class' => 'control-label col-sm-2']) !!}

                        <div class="col-sm-10">
                            {!! Form::text('date_raised', null, ['class' => 'form-control date datetime', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}
                    @if ($errors->has('date_raised'))
                    <span class="help-block">
                        <strong>{{ $errors->first('date_raised') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div> -->
                    <div class="form-group {{ $errors->has('business_priority') ? 'has-error' : '' }}">
                        {!! Form::label('business_priority', 'Business Priority', ['class' => 'control-label col-sm-2']) !!}
                        <div class="col-sm-10">
                            {!! Form::select('business_priority', $businessPrioritySelect, $form->business_priority, ['class' => 'form-control col-sm-10', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}
                            @if ($errors->has('business_priority'))
                                <span class="help-block">
                            <strong>{{ $errors->first('business_priority') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('change_start_date') ? 'has-error' : '' }}">
                        {!! Form::label('change_start_date', 'Change Start Date', ['class' => 'control-label col-sm-2']) !!}

                        <div class="col-sm-10" style="margin-bottom: 15px;">
                            {!! Form::text('change_start_date', $form->change_start_date, ['class' => 'form-control date datetime', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}
                            @if ($errors->has('change_start_date'))
                                <span class="help-block">
                            <strong>{{ $errors->first('change_start_date') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('change_start_time') ? 'has-error' : '' }}">
                        {!! Form::label('change_start_time', 'Change Start Time', ['class' => 'control-label col-sm-2']) !!}

                        <div class="col-sm-10" style="margin-bottom: 15px;">
                            {!! Form::time('change_start_time', $form->change_start_time, ['class' => 'form-control timepicker', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}
                            @if ($errors->has('change_start_time'))
                                <span class="help-block">
                            <strong>{{ $errors->first('change_start_time') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('change_end_date') ? 'has-error' : '' }}">
                        {!! Form::label('change_end_date', 'Change End Date', ['class' => 'control-label col-sm-2']) !!}

                        <div class="col-sm-10" style="margin-bottom: 15px;">
                            {!! Form::text('change_end_date', $form->change_end_date, ['class' => 'form-control date datetime', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}
                            @if ($errors->has('change_end_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('change_end_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('change_end_time') ? 'has-error' : '' }}">
                        {!! Form::label('change_end_time', 'Change End Time', ['class' => 'control-label col-sm-2']) !!}

                        <div class="col-sm-10" style="margin-bottom: 15px;">
                            {!! Form::time('change_end_time', $form->change_end_time, ['class' => 'form-control timepicker2', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}

                            @if ($errors->has('change_end_time'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('change_end_time') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    @can('is_CA')
                    <!-- START STATUS -->
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            {!! Form::label('status', 'Status', ['class' => 'control-label col-sm-2']) !!}
                            <div class="col-sm-10">
                                {!! Form::select('status', $status, $form->status, ['class' => 'form-control col-sm-10', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}
                                @if ($errors->has('status'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('status') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div><!-- END STATUS -->

                        <!-- START SCHEDULE STATUS -->
                        <div class="form-group {{ $errors->has('complete_status') ? 'has-error' : '' }}">
                            {!! Form::label('complete_status', 'Complete Status', ['class' => 'control-label col-sm-2']) !!}
                            <div class="col-sm-10">
                                {!! Form::select('complete_status', $completeStatus, $form->complete_status, ['class' => 'form-control col-sm-10', 'style' => 'margin-bottom: 15px;','required' =>'required']) !!}
                                @if ($errors->has('complete_status'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('complete_status') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div><!-- END SCHEDULE STATUS -->
                        <div class="form-group {{ $errors->has('complete_date') ? 'has-error' : '' }}" style="display: none" id="complete_date_block">
                            {!! Form::label('complete_date', 'Complete Date', ['class' => 'control-label col-sm-2']) !!}
                            <div class="col-sm-10">
                                {!! Form::text('complete_date', empty($form->complete_date) ? date('Y-m-d', strtotime($form->change_end_date)) : $form->complete_date, ['class' => 'form-control col-sm-10 date datetime', 'style' => 'margin-bottom: 15px;', 'disabled']) !!}
                                @if ($errors->has('complete_date'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('complete_date') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div><!-- END SCHEDULE STATUS -->
                    @endcan
                </div>
                <h4>Proposed Change (What change is required)</h4>
                <div class="form-group {{ $errors->has('proposed_change') ? 'has-error' : '' }}">
                    {!! Form::textarea('proposed_change', $form->proposed_change, ['class' => 'form-control', 'rows' => '3','required' =>'required','id'=>'proposed_change']) !!}
                    @if ($errors->has('proposed_change'))
                        <span class="help-block">
                            <strong>{{ $errors->first('proposed_change') }}</strong>
                        </span>
                    @endif
                </div>
                <h4>Reasons for Change (include justification and benefits)</h4>
                <div class="form-group {{ $errors->has('reason') ? 'has-error' : '' }}">
                    {!! Form::textarea('reason', $form->reason, ['class' => 'form-control', 'rows' => '3','required' =>'required','id'=>'reason']) !!}
                    @if ($errors->has('reason'))
                        <span class="help-block">
                            <strong>{{ $errors->first('reason') }}</strong>
                        </span>
                    @endif
                </div>
                <h4>Risk Assessment </h4>
                <div class="form-group {{ $errors->has('risk_assessment') ? 'has-error' : '' }}">
                    {!! Form::textarea('risk_assessment', $form->risk_assessment, ['class' => 'form-control', 'rows' => '3','required' =>'required','id'=>'risk_assessment']) !!}
                    @if ($errors->has('risk_assessment'))
                        <span class="help-block">
                            <strong>{{ $errors->first('risk_assessment') }}</strong>
                        </span>
                    @endif
                </div>
                <h4> Roll Back Strategy</h4>
                <div class="form-group {{ $errors->has('rollback_strategy') ? 'has-error' : '' }}">
                    {!! Form::textarea('rollback_strategy', $form->rollback_strategy, ['class' => 'form-control', 'rows' => '3','required' =>'required','id'=>'rollback_strategy']) !!}
                    @if ($errors->has('rollback_strategy'))
                        <span class="help-block">
                            <strong>{{ $errors->first('rollback_strategy') }}</strong>
                        </span>
                    @endif
                </div>
                <h4>Test Plans</h4>
                <div class="form-group {{ $errors->has('test_plan') ? 'has-error' : '' }}">
                    {!! Form::textarea('test_plan', $form->test_plan, ['class' => 'form-control', 'rows' => '3','required' =>'required','id'=>'test_plan']) !!}
                    @if ($errors->has('test_plan'))
                        <span class="help-block">
                            <strong>{{ $errors->first('test_plan') }}</strong>
                        </span>
                    @endif
                </div>
                <h4>Authorisation Signature </h4>
                <div class="form-group {{ $errors->has('authorisation_signature') ? 'has-error' : '' }}">
                    {!! Form::text('authorisation_signature', $form->authorisation_signature, ['class' => 'form-control','required' =>'required','id'=>'authorisation_signature']) !!}
                    @if ($errors->has('authorisation_signature'))
                        <span class="help-block">
                            <strong>{{ $errors->first('authorisation_signature') }}</strong>
                        </span>
                    @endif
                </div>
                <h4>Files <button type="button" id="add-file-btn" class="btn btn-success"><i class="fa fa-plus"></i></button></h4>
                <div class="form-group" id="files-area">
                <ul>
                {!! Form::hidden('form_id', $form->id,['id'=> 'form_id']) !!}
                @foreach ($files as $file)
                    <li><a href="{{config('file.upload_url').'/'.$file->file_name}}" download>{{ $file->file_name }}</a><button type="button" class="remove-uploaded-file btn btn-danger"><i class="fa fa-times"></i></button></li> 
                @endforeach
                </ul>
                </div>
                <h4>Next available CAB date to discuss</h4>
                <div class="form-group {{ $errors->has('planned_date') ? 'has-error' : '' }}">
                    {!! Form::text('planned_date', $form->planned_date, ['class' => 'form-control date', 'required' =>'required', 'id' => 'planned_date' ]) !!}
                    @if ($errors->has('planned_date'))
                        <span class="help-block">
                            <strong>{{ $errors->first('planned_date') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" id="save-form-btn" class="btn btn-info pull-right">save</button>
            </div>
            {!! Form::close() !!}
            {!! Form::open(['action' => ['DashboardController@removeUploadedFile'],'method' => 'POST','class' => 'inline','id' => 'file_form']) !!}
            {!! Form::hidden('file_file_name', null,['id'=> 'file_file_name']) !!}
            {!! Form::hidden('file_form_id', null,['id'=> 'file_form_id']) !!}
            {!! Form::close() !!}
        </div>
    </div>


@stop
@section('js')
    <script>
        var CABPaddingDayNum = 3;
        var status = {{ $form->status }};
        var default_status = {{config('form.status.pending_CAB')}};
        var change_type = {{ $form->change_type }};
        var default_change_type = {{config('form.change_type.self_approve')}};
        if ( {{Auth::user()->id}} != {{$form->raised_by_user_id}} && {{Auth::user()->role}} != {{config('user.role.CA')}} ){
            $('#raised_by_user_id').prop('disabled',true);
            $('#contact_no').prop('disabled',true);
            $('#change_type').prop('disabled',true);
            $('#change_title').prop('disabled',true);
            $('#proposed_change_owner_user_id').prop('disabled',true);
            $('#business_priority').prop('disabled',true);
            $('#change_start_date').prop('disabled',true);
            $('#change_start_time').prop('disabled',true);
            $('#change_end_date').prop('disabled',true);
            $('#change_end_time').prop('disabled',true);
            $('#proposed_change').prop('disabled',true);
            $('#reason').prop('disabled',true);
            $('#risk_assessment').prop('disabled',true);
            $('#rollback_strategy').prop('disabled',true);
            $('#test_plan').prop('disabled',true);
            $('#authorisation_signature').prop('disabled',true);
            $('.remove-uploaded-file').prop('disabled',true);
            $('#add-file-btn').prop('disabled',true);
            $('#save-form-btn').prop('disabled',true);
        }else if ({{Auth::user()->id}} == {{$form->raised_by_user_id}} ){
            if (change_type == default_change_type || {{Auth::user()->role}} == {{config('user.role.CA')}}){
                //Do nothing (do not disable any of the field) 
            } else {
                if (default_status != status){
                    $('#raised_by_user_id').prop('disabled',true);
                    $('#contact_no').prop('disabled',true);
                    $('#change_type').prop('disabled',true);
                    $('#change_title').prop('disabled',true);
                    $('#proposed_change_owner_user_id').prop('disabled',true);
                    $('#business_priority').prop('disabled',true);
                    $('#change_start_date').prop('disabled',true);
                    $('#change_start_time').prop('disabled',true);
                    $('#change_end_date').prop('disabled',true);
                    $('#change_end_time').prop('disabled',true);
                    $('#proposed_change').prop('disabled',true);
                    $('#reason').prop('disabled',true);
                    $('#risk_assessment').prop('disabled',true);
                    $('#rollback_strategy').prop('disabled',true);
                    $('#test_plan').prop('disabled',true);
                    $('#authorisation_signature').prop('disabled',true);
                }
            }

        }
        
        $('.remove-uploaded-file').on('click',function(){
            $('#file_form_id').val($('#form_id').val());
            $('#file_file_name').val($(this).parent().find('a').text());
            $('#file_form').submit();
        });

        function dateChanged() {
            $(this).datetimepicker('hide');

            var d = new Date();
            var nextCABDay = d.setDate(d.getDate() + (CABPaddingDayNum + 7 - d.getDay()) % 7);
            nextCABDay = moment(nextCABDay).format('YYYY-MM-DD');
            var selectedDate = new Date($(this).val()).toISOString().slice(0, 10);
            var currentChangeType = $('#change_type').val();
            if (selectedDate < nextCABDay && currentChangeType != '{{ config('form.change_type.self_approve') }}') {
                $('#change_type').val('{{ config('form.change_type.emergency') }}');
            }
        }
        var today = moment(new Date()).format('YYYY-MM-DD');



        $(document).ready(function() {  
            var role = {{Auth::user()->role}};

            if (role == {{ config('user.role.CR') }} )
            {
                $("#planned_date").prop("readonly", true);
                
            }else{
                $("#planned_date").addClass("custom-datepicker");
            }
            
            if ($('input[name="planned_date"]').length == 0) {
                var d = new Date();
                var nextCABDay = d.setDate(d.getDate() + (CABPaddingDayNum + 7 - d.getDay()) % 7);
                nextCABDay = moment(nextCABDay).format('YYYY-MM-DD');
                $('input[name="planned_date"]').val(nextCABDay);
            }

            $('#change_start_date').change(function() {
                var d = new Date();
                var nextCABDay = d.setDate(d.getDate() + (CABPaddingDayNum + 7 - d.getDay()) % 7);
                nextCABDay = moment(nextCABDay).format('YYYY-MM-DD');

                var selectedDate = new Date($(this).val()).toISOString().slice(0, 10);
                var currentChangeType = $('#change_type').val();
                if (selectedDate < nextCABDay && currentChangeType != '{{ config('form.change_type.self_approve') }}') {
                    $('#change_type').val('{{ config('form.change_type.emergency') }}');
                }
            });

            //If current change_type is self_approve, change the #change_start_date to pickable in the past
            var currentChangeType = $('#change_type').val();
            if (currentChangeType == '{{ config('form.change_type.self_approve') }}'){
                $('#change_start_date').datetimepicker('remove');
                $('#change_start_date').datetimepicker({
                    minView: 2,
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });
            }
			if($('#change_type').val() == '{{ config('form.change_type.bypass') }}'){
					$('#status').val({{ config('form.status.approved') }});
					$('#status').attr('disabled','disabled');
					$('#complete_status').val({{ config('form.complete_status.success') }});
					$('#complete_status').attr('disabled','disabled');
					
					
				}

            $('#change_type').change(function () {
                //Reset start time after change
				if($(this).val() == '{{ config('form.change_type.bypass') }}'){
					$('#status').val({{ config('form.status.approved') }});
					$('#status').attr('readonly','readonly');
					$('#status option:not(:selected)').attr('disabled', true);
					$('#complete_status').val({{ config('form.complete_status.success') }});
					$('#complete_status').attr('readonly','readonly');
					$('#complete_status option:not(:selected)').attr('disabled', true);
				}else{
					$('#status').val('');
					$('#status').removeAttr('readonly');
					$('#status option:not(:selected)').removeAttr('disabled');
					$('#complete_status').val('');
					$('#complete_status').removeAttr('readonly');
					$('#complete_status option:not(:selected)').removeAttr('disabled');
				}
				
				
				
                if ($(this).val() == '{{ config('form.change_type.self_approve') }}'){
                    $('#change_start_date').datetimepicker('remove');
                    $('#change_start_date').datetimepicker({
                        minView: 2,
                        format: 'yyyy-mm-dd',
                        autoclose: true
                    });
	                $('#complete_date').datetimepicker('remove');
	                $('#complete_date').datetimepicker({
		                minView: 2,
		                format: 'yyyy-mm-dd',
		                autoclose: true
	                });
                    return;
                } else {
                    $('#change_start_date').datetimepicker('remove');
                    $('#change_start_date').datetimepicker({
                        minView: 2,
                        format: 'yyyy-mm-dd',
                        startDate: new Date(),
                        autoclose: true
                    });
	                $('#complete_date').datetimepicker('remove');
	                $('#complete_date').datetimepicker({
		                minView: 2,
		                format: 'yyyy-mm-dd',
		                startDate: new Date(),
		                autoclose: true
	                });
                    if ($(this).val() == '{{ config('form.change_type.emergency') }}')
                        return;

                    var d = new Date();
                    var nextCABDay = d.setDate(d.getDate() + (CABPaddingDayNum + 7 - d.getDay()) % 7);
                    nextCABDay = moment(nextCABDay).format('YYYY-MM-DD');
                    var selectedDate = new Date($('#change_start_date').val()).toISOString().slice(0, 10);
                    if (selectedDate < nextCABDay) {
                        alert('Please change start date');
                        $(this).val('{{ config('form.change_type.emergency') }}')
                    }
                }
	            $('#change_start_date').val('');
            });

            $("#contact_no").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                        // let it happen, don't do anything
                        return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        
            
            $(".custom-datepicker").datetimepicker({
                minView: 2,
                format: 'yyyy-mm-dd',
                autoclose: true,

            }).change(dateChanged).on('changeDate', dateChanged);

            $('#change_start_date, #change_start_time').on('change', function() {
                $('#change_end_date').val('');
                $('#change_end_time').val('');

                $('#change_end_date').datetimepicker('remove');
                $('#change_end_time').datetimepicker('remove');

                $('#change_end_date').datetimepicker({
                    minView: 2,
                    startDate: new Date($('#change_start_date').val() + " 00:00:00"),
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });

                var startDate = new Date($('#change_start_date').val() + ' ' + $('#change_start_time').val());
                //Add 5 minutes
                startDate = moment(startDate).add(5, 'm').toDate();
                if ($('#change_end_date').val() > $('#change_start_date').val()) {
                    startDate = new Date($('#change_start_date').val() + ' 00:00:00');
                }

                $('#change_end_time').datetimepicker({
                    showMeridian:false,
                    startDate: startDate,
                    autoclose: true,
                    minView: 0,
                    maxView: 1,
                    startView: 1,
                    format: 'hh:ii',
                    pickerPosition: 'bottom-left'
                });
            });

            $('#change_end_date').on('change', function() {
                var startDate = new Date($('#change_start_date').val() + ' ' + $('#change_start_time').val());
                //Add 5 minutes
                startDate = moment(startDate).add(5, 'm').toDate();
                if ($('#change_end_date').val() > $('#change_start_date').val()) {
                    startDate = new Date($('#change_start_date').val() + ' 00:00:00');
                }

                $('#change_end_time').datetimepicker('remove');
                $('#change_end_time').datetimepicker({
                    showMeridian:false,
                    startDate: startDate,
                    autoclose: true,
                    minView: 0,
                    maxView: 1,
                    startView: 1,
                    format: 'hh:ii',
                    pickerPosition: 'bottom-left'
                });
				
				if($('#change_type').val() == '{{ config('form.change_type.bypass') }}'){
					var completeDate = new Date($(this).val());
					$('#complete_date').datetimepicker('remove');
					$('#complete_date').datetimepicker({
						defaultDate: completeDate,
						format: 'yyyy-mm-dd',
						autoclose: true
					});
					$('#complete_date').datetimepicker("setDate" , completeDate,{
					});
					$('#complete_date').attr('disabled','disabled');
				}
				
            })
			
		
			
			
			
	        $('#complete_status').trigger('change');
        });

        $('#add-file-btn').click(function() {
            var html = '<div class="file-item">' +
                '<input type="file" class="form-control" name="files[]" required style="display: inline; width: 96.6%"> ' +
                '<button type="button" class="remove-file-btn btn btn-danger"><i class="fa fa-times"></i></button>' +
                '</div>';
            $('#files-area').append(html);
        });

        $('body').on('click', '.remove-file-btn', function() {
            $(this).closest('.file-item').remove();
        });

        $('#complete_status').change(function() {
	        if ($(this).val() === '{{ config('form.complete_status.success')}}' || $(this).val() === '{{ config('form.complete_status.unsuccess') }}') {
	        	if ($('#status').val() === '{{ config('form.status.pending_CAB') }}') {
	        		$('#status').val({{ config('form.status.approved') }})
                }

		        $('#complete_date_block').show();
		        $('#complete_date').removeAttr('disabled');
	        }
	        else {
		        $('#complete_date_block').hide();
		        $('#complete_date').attr('disabled');
	        }
        });

        $('#status').change(function() {
        	if ($(this).val() === '{{ config('form.status.pending_CAB') }}') {
        		if ($('#complete_status').val() === '{{ config('form.complete_status.success')}}' || $('#complete_status').val() === '{{ config('form.complete_status.unsuccess') }}') {
        			alert('Please change Complete status to appropriate value');
        			$(this).val({{ config('form.status.approved') }});
                }
            }
        })
    </script>
@stop
