<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'pin', 
        'date_time', 
        'ver', 
        'status_absen_id', 
        'mesin_id'
    ];
}
