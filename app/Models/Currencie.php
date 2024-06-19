<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currencie extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'entry_price', 'tp1', 'tp2', 'tp3', 'tp4', 'tp5', 'status', 'sgy_type'];
    // protected $guarded=[];
    // protected $table = '';


}
