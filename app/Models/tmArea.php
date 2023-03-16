<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmArea extends Model
{
    use HasFactory;

    protected $table = 'tm_areas';

    protected $guarded = ['id'];

    public function checkout()
    {
        return $this->hasMany(TtCheckout::class);
    }
}
