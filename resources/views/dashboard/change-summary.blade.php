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
    <h1 style="background-color: #ffffff; margin: -15px -15px 0px -15px; padding: 15px 20px; color: #5EA1D6;">Change
        Summary</h1>
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
                    <!-- <th>Raised By</th> -->
                    <th>Proposed Change Owner</th>
                    <th>Team</th>
                    <th>Raised Date</th>
                    <th>Approved By</th>
                    <th>Smart Hub Impact</th>
                    <th>Planned Date</th>
                    <th>Implemented</th>
                    <th>Status</th>
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
                    //update proposed change name
                    $proposed_change_owner_user = DB::table('users')->where('id','=',$form->proposed_change_owner_user_id)->first();
                    ?>
                    <tr>
                        <td><a href="{{ action('DashboardController@changeSummaryDetail', $form->reference) }}" target="blank">{{ $form->reference }}</a></td>
                        <td>{{ $form->change_title }}</td>
                        <td>{{ $proposed_change_owner_user ? $proposed_change_owner_user->name : 'Unknown'   }}</td>
                        <td>{{ $form->raisedBy->teamName() }}</td>
                        <td>{{ $form->date_raised }}</td>
                        <td>{{ $approved_rejected_by }}</td>

                        <td>
                            @if($form->smart_hub_impact ==config('form.smart_hub_impact.yes'))
                                Yes
                            @else
                                No
                            @endif
                        </td>

                        <td>{{ "$form->change_start_date $form->change_start_time - $form->change_end_date $form->change_end_time" }}</td>
                        <td>{{ $form->complete_date }}</td>
                        <td>
                            <?php
                            $status_text = '';
                            if ($form->status == config('form.status.approved'))
							{
                            	$status_text = 'Approved';
								if($form->complete_status == config('form.complete_status.success'))
									$status_text = 'Approved (Successful)';
								if($form->complete_status == config('form.complete_status.unsuccess'))
									$status_text = 'Approved (Unsuccess)';
								if($form->complete_status == config('form.complete_status.planned'))
									$status_text = 'Approved (Planned)';
								if($form->complete_status == config('form.complete_status.waiting_for_complete'))
									$status_text = 'Approved (Waiting for complete)';
							}
                            else if ($form->status == config('form.status.rejected'))
							{
								$status_text = 'Rejected';
								if($form->complete_status == config('form.complete_status.success'))
									$status_text = 'Rejected (Successful)';
								if($form->complete_status == config('form.complete_status.unsuccess'))
									$status_text = 'Rejected (Unsuccess)';
								if($form->complete_status == config('form.complete_status.planned'))
									$status_text = 'Rejected (Planned)';
								if($form->complete_status == config('form.complete_status.waiting_for_complete'))
									$status_text = 'Rejected (Waiting for complete)';
							}
                            else if ($form->status == config('form.status.pending_CAB'))
							{
                            	$status_text = 'Pending CAB';
								if($form->complete_status == config('form.complete_status.success'))
									$status_text = 'Pending CAB (Successful)';
								if($form->complete_status == config('form.complete_status.unsuccess'))
									$status_text = 'Pending CAB (Unsuccess)';
								if($form->complete_status == config('form.complete_status.planned'))
									$status_text = 'Pending CAB (Planned)';
								if($form->complete_status == config('form.complete_status.waiting_for_complete'))
									$status_text = 'Pending CAB (Waiting for complete)';
                            }
							?>
                            @if ($form->is_closed == 1 && $form->complete_status == config('form.complete_status.success') )
                                <span title="Successful">Successful</span>
                            @elseif ($form->is_closed == 1 && $form->complete_status == config('form.complete_status.unsuccess') )
                            <span title="Unsuccessful">Unsuccessful</span>
                            @elseif ($form->is_closed == 1 && $form->complete_status == config('form.complete_status.bypass') )
                            <span title="Bypass">Bypass</span>
                            @else
                                {{ $status_text }}
                            @endif
                        </td>
                        <td>
                            @if($form->change_type == config('form.change_type.normal') )
                                Normal
                            @elseif($form->change_type == config('form.change_type.self_approve') )
                                Self Approved
							 @elseif($form->change_type == config('form.change_type.bypass') )
                                By Pass
                            @else
                                Emergency
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
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
