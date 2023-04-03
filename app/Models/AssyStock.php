<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssyStock extends Model
{
    use HasFactory;

    protected $table = 'assy_stocks';

    protected $guarded = ['id'];
}
