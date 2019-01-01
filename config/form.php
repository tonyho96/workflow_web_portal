<?php
return [
    'change_type' => [
        'normal' => 1,
        'emergency' => 2,
	    'self_approve' => 3,
		'bypass' => 4
    ],
    'business_priority' => [
        'low' => 1,
        'medium_essential_enhancement' => 2,
        'medium_problem_issue_resolution' => 3,
        'high' => 4,
        'business_critical' => 5
    ],
    'status' => [
        'pending_CAB' => 1,
        'approved' => 2,
        'rejected' => 3
    ],
    'complete_status' => [
        'planned' => 1,
        'waiting_for_complete' => 2,
        'success' => 3,
        'unsuccess' => 4
        
    ],
    'export_path' => public_path('csv'),
    'import_path' => public_path('csv'),
    'imported_csv' => [
        'id' => 'id',
        'raised_by_user_name' => 'Raised By',
        'contact_no' => 'Contact No',
        'change_type' => 'Change Type',
        'change_title' => 'Change Title',
        'proposed_change_owner_user_name' => 'Proposed Change Owner',
        'date_raised' => 'Date Raised',
        'business_priority' => 'Business Priority',
        'change_start_date' => 'Change Start Date',
        'change_start_time' => 'Change Start Time',
        'change_end_date' => 'Change End Date',
        'change_end_time' => 'Change End Time',
        'proposed_change' => 'Proposed Change (What change is required)',
        'reason' => 'Reasons for Change (include justification and benefits)',
        'risk_assessment' => 'Risk Assessment',
        'rollback_strategy' => 'Roll Back Strategy',
        'test_plan' => 'Test Plans',
        'authorisation_signature' => 'Authorisation Signature'
    ],
    'export_option' => [
        'all' => 'all-users',
        'specific' => 'specific-user'
    ],
	'smart_hub_impact'=>[
		'yes'=>1,
		'no'=>0
	]
];
