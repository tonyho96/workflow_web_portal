<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        table{
            border-collapse: collapse;
        }
        th, td{
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>
<body>
<table class="table">
    <thead>
    <tr>
        <th>Reference</th>
        <th>Title</th>
        <th>Raised By</th>
        <th>Raised Date</th>
        <th>Approve By</th>
        <th>Approve Date</th>
        <th>Implemented</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($data['cr_users'] as $cr)
        <?php
            $user_raised = DB::table('users')->where('id', '=', $cr->raised_by_user_id)->first();
            $approved_rejected_by = '';
            if( ! empty( $cr->approved_rejected_by ) ) {
                $user_approved_rejected = DB::table('users')->where('id', '=', $cr->approved_rejected_by)->first();
                $approved_rejected_by = $user_approved_rejected->name;
            }
        ?>
        <tr>
            <td>{{ $cr->reference }} </td>
            <td>{{ $cr->change_title }} </td>
            <td>{{ $user_raised->name }} </td>
            <td>{{ $cr->date_raised }} </td>
            <td>{{ $approved_rejected_by }} </td>
            <td>{{ $cr->approved_rejected_date }} </td>
            <td>@if($cr->complete_status == config('form.complete_status.success') ||
                        $cr->complete_status == config('form.complete_status.bypass'))
                    {{$cr->completion_signature_date}}
                @elseif($cr->status == config('form.status.rejected') ||
                                $cr->complete_status == config('form.complete_status.unsuccess'))
                    Fall roll back
                @endif
            </td>
            <td>@if ( $cr->status == config('form.status.approved') )
                    Approved
                @elseif ( $cr->status == config('form.status.rejected') )
                    Rejected
                @elseif ( $cr->status == config('form.status.pending_CAB') )
                    Pending CAB
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
