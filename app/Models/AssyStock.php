<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssyStock extends Model
{
    use HasFactory;

    protected $table = 'assy_stocks';

    protected $guarded = ['id'];

    public function part()
    {
        return $this->belongsTo(TmPart::class);
    }
}
