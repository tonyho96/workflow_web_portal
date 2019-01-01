jQuery(document).ready(function($) {
    if( $(".timepicker").length ) {
        $('.timepicker').datetimepicker({
            showMeridian:false,
            // minuteStep: 5,
            autoclose: true,
            minView: 0,
            maxView: 1,
            startView: 1,
            format: 'hh:ii',
            pickerPosition: 'bottom-left'
        });
        // $('.timepicker').data('datetimepicker').picker.addClass('timepicker');
    }
    if( $(".sTime").length ) {
        $('.sTime').datetimepicker({
            showMeridian: true,
            minuteStep: 5,
            autoclose: true,
            minView: 0,
            maxView: 1,
            startView: 1,
            format: 'H:ii P',
            pickerPosition: 'bottom-left'
        });
        $('.sTime').data('datetimepicker').picker.addClass('timepicker');
    }
    var today = new Date();
    var endDate=new Date($("#planned_date").val());
    endDate.setDate(endDate.getDate()-1);
    if( $(".datetime").length ) {
        // $(".datepicker").datepicker();
          $(".datetime").datetimepicker({
            minView: 2,
            startDate: today,
            // endDate: endDate,
            format: 'yyyy-mm-dd',
            autoclose: true
            });
    }
    // $('.datetime').datetimepicker();

    $(document).on( 'click', '.form-approved', function(event) {
        event.preventDefault();
        var form_id = $(this).attr( 'form-id' );
        $('#approve_reject_action').val('form-approved');
        $('#approve_reject_form_id').val(form_id);
	    $('#approve-reject-modal').modal()
    } );

    $(document).on( 'click', '.form-rejected', function(event) {
	    event.preventDefault();
	    var form_id = $(this).attr('form-id');
	    $('#approve_reject_action').val('form-rejected');
	    $('#approve_reject_form_id').val(form_id);
	    $('#approve-reject-modal').modal();


    });

    $(document).on('click', '#approve-reject-btn', function() {
        var action = $('#approve_reject_action').val();
        var form_id = $('#approve_reject_form_id').val();
        var reason = $('#approved_rejected_reason').val();
        var approved_rejected_by = $('#approved_rejected_by').val();

        if (reason.length === 0) {
            alert('Please enter reason');
            return false;
        }
        $(this).attr('disabled', 'disabled');
	    $.ajax({
		    url: action,
		    type: 'GET',
		    dataType: 'json',
		    data: {
			    form_id: form_id,
			    reason: reason,
			    approved_rejected_by: approved_rejected_by
		    },
		    success: function (response) {
		    },
		    complete: function () {
			    location.reload();
		    }
	    });
    });
});