<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    protected $fillable = [
        'user_type', 'user_id', 'complaint_id', 'action',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
