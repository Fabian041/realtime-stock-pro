<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmPart extends Model
{
    use HasFactory;

    protected $table = 'tm_parts';

    protected $guarded = ['id'];

    public function checkout()
    {
        return $this->hasMany(TtCheckout::class);
    }
}
