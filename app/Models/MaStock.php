<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaStock extends Model
{
    use HasFactory;

    protected $table = 'ma_stocks';

    protected $guarded = ['id'];

    public function part()
    {
        return $this->belongsTo(TmPart::class);
    }
}
