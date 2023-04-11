<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcStock extends Model
{
    use HasFactory;

    protected $table = 'dc_stocks';

    protected $guarded = ['id'];
}
