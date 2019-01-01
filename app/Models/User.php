<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
use Illuminate\Notifications\Notifiable;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_working', 'is_approved', 'team'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isCA()
    {
        return $this->role == config('user.role.CA');
    }

    public function sendPasswordResetNotification($token)
    {
        $user_email = $this->getEmailForPasswordReset();
        $this->notify(new CustomResetPassword($token, $user_email));
    }

    public function teamName() {
    	return $this->team == config('user.team.ATC') ? 'ATC' : 'SHS';
    }
}
