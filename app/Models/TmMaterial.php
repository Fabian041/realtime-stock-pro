<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmMaterial extends Model
{
    use HasFactory;
    protected $table = 'tm_materials';

    protected $guarded = ['id']; 
}