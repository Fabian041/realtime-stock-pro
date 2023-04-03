<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaStock extends Model
{
    use HasFactory;

    protected $table = 'ma_stocks';

    protected $guarded = ['id'];
}
