<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'students';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'Stud_name',
        'Stud_email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'Student_id');
    }
}
