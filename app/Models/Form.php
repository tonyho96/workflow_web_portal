<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{

    protected $fillable = [
        'raised_by_user_id',
        'contact_no',
        'change_type',
        'change_title',
        'proposed_change_owner_user_id',
        'date_raised',
        'business_priority',
        'change_start_date',
        'change_start_time',
        'change_end_date',
        'change_end_time',
        'proposed_change',
        'reason',
        'risk_assessment',
        'rollback_strategy',
        'test_plan',
        'authorisation_signature',
        'authorisation_signature_date',
        'completion_notes',
        'completion_signature',
        'completion_signature_date',
        'author_user_id',
        'reference',
        'status',
        'complete_status',
        'planned_date',
        'approved_rejected_reason',
        'approved_rejected_date',
        'approved_rejected_by',
	    'is_closed',
	    'complete_date',
		'smart_hub_impact'
    ];

    protected $table = 'forms';

    public function author()
    {
        return $this->belongsTo('App\Models\User', 'author_user_id', 'id');
    }

    public function proposedChangeOwner()
    {
        return $this->belongsTo('App\Models\User', 'proposed_change_owner_user_id', 'id');
    }

    /*public function proposedChangeOwner2() {
        return $this->belongsTo('App\Models\User', 'proposed_change_owner_2_user_id', 'id');
    }*/

    public function raisedBy()
    {
        return $this->belongsTo('App\Models\User', 'raised_by_user_id', 'id');
    }

    public function changeType()
    {
        $changeTypes = config('form.change_type');
        $flip = array_flip($changeTypes);
        return $flip[$this->change_type];
    }

    public function status()
    {
        $statuses = config('form.status');
        $flip = array_flip($statuses);
        return $flip[$this->status];
    }

    public function statusFormat() {
	    return ucfirst(str_replace("_", " ", $this->status()));
    }

    public function completeStatus()
    {
        $completeStatuses = config('form.complete_status');
        $flip = array_flip($completeStatuses);
        return $flip[$this->complete_status];
    }

    public function completeStatusFormat() {
    	return ucfirst(str_replace("_", " ", $this->completeStatus()));
    }

    public function businessPriority()
    {
        $priorities = config('form.business_priority');
        $flip = array_flip($priorities);
        return $flip[$this->business_priority];
    }

	public function approvedRejectedUser() {
		return $this->belongsTo('App\Models\User', 'approved_rejected_by', 'id');
	}

	public function files() {
    	return $this->hasMany('App\Models\File', 'form_id', 'id');
	}
}
