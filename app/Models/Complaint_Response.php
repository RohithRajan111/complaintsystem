<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint_Response extends Model
{
    /** @use HasFactory<\Database\Factories\ComplaintResponseFactory> */
    use HasFactory;

    protected $fillable = [
        'Complaint_id',
        'Student_id',
        'Dept_id',
        'response',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'Complaint_id');
    }

    public function department()
    {
        return $this->belongsTo(Dept::class, 'Dept_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'Student_id');
    }
}
