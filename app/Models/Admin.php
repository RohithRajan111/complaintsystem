<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admins';

    // ** ADD THESE THREE LINES TO FIX THE SESSION ERROR **
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'Admin_email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function routeNotificationForMail($notification)
    {
        // Return the email address from the correct column
        return $this->Admin_email; // <-- Change this to match your column name
    }


}
