<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmTransaction extends Model
{
    use HasFactory;

    protected $table = 'tm_transactions';

    protected $guarded = ['id'];
}
