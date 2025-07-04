<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Dept extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'depts';

    // ** ADD THESE THREE LINES TO FIX THE SESSION ERROR **
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'Dept_name',
        'Hod_name',
        'Dept_email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'Dept_id');
    }
}
