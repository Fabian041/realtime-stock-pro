<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TtMaterial extends Model
{
    use HasFactory;
    
    protected $table = 'tt_materials';

    protected $guarded = ['updated_at']; 
}