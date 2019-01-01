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
    <h1 style="background-color: #ffffff; margin: -15px -15px 0px -15px; padding: 15px 20px; color: #5EA1D6;">Approve /
        Reject change</h1>
@stop
@section('content')
    <div id="approve-reject" style="background-color: #ffffff; min-height: 100vh; margin: -15px;">
        <br/>
        <div class="table-responsive" style="padding: 15px;">
            <table class="datatables">
                <thead>
                <tr>
                    <th>Reference</th>
                    <th>Title</th>
                    <th>Raised By</th>
                    <th>Raised Date</th>
                    <th>Approved By</th>
                    <th>Approved Date</th>
                    <th>Implemented</th>
                    <th>RFC status</th>
                    <th>Reason</th>
                    <th>Approve / Reject</th>
                    <th>Change Type</th>
                </tr>
                </thead>
                <tbody>
                @foreach( $forms as $form )
                    <?php
                    $user_raised = DB::table('users')->where('id', '=', $form->raised_by_user_id)->first();
                    $approved_rejected_by = '';
                    if (!empty($form->approved_rejected_by)) {
                        $user_approved_rejected = DB::table('users')->where('id', '=', $form->approved_rejected_by)->first();
	                    $approved_rejected_by = $user_approved_rejected ? $user_approved_rejected->name : 'Unknown';
                    }
                    ?>
                    <tr>
                        <td>{{ $form->reference }}</td>
                        <td>{{ $form->change_title }}</td>
                        <td>{{ $user_raised ? $user_raised->name : 'Unknown'   }}</td>
                        <td>{{ $form->date_raised }}</td>
                        <td>{{ $approved_rejected_by }}</td>
                        <td>{{ $form->approved_rejected_date }}</td>
                        <td></td>
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
                            @if ($form->status != config('form.status.pending_CAB'))
                                {{ $form->approved_rejected_reason }}
                            @endif
                        </td>
                        <td>
                            @if ( $form->status == config('form.status.pending_CAB') )
                                {!! Form::button('Approve', ['class' => 'btn btn-sm btn-success form-approved', 'form-id' => $form->id ]) !!}

                                {!! Form::button('Reject', ['class' => 'btn btn-sm btn-danger form-rejected', 'form-id' => $form->id ]) !!}
                            @endif
                        </td>
                        <td>
                            @if ($form->change_type == config('form.change_type.normal'))
                                Normal
                            @elseif ($form->change_type == config('form.change_type.emergency'))
                                Emergency
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="approve-reject-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="exampleModalLabel">Approve/Reject Change</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::hidden('approve_reject_action', 'approve', ['id' => 'approve_reject_action']) !!}
                {!! Form::hidden('approve_reject_form_id', '', ['id' => 'approve_reject_form_id']) !!}

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="box box-default">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {!! Form::label('approved_rejected_reason', 'Reason', ['class' => '']) !!}
                                            {!! Form::textarea('approved_rejected_reason', null,['class' => 'form-control','required' =>'required','style'=>'width:500px']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {!! Form::label('approved_rejected_by', 'By', ['class' => '']) !!}
                                            {!! Form::select('approved_rejected_by', \App\Models\User::all()->pluck('name', 'id')->all(), Auth::user()->id ,['class' => 'form-control','required' =>'required', 'style'=>'width:500px']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="approve-reject-btn" name="save" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.datatables').DataTable({
                searching: false,
                paging: false
            });
        });
    </script>
@stop