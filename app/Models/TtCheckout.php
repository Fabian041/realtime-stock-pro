<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TtCheckout extends Model
{
    use HasFactory;

    protected $table = 'tt_checkouts';

    protected $guarded = ['id'];

    public function part()
    {
        return $this->belongsTo(TmPart::class);
    }

    public function area()
    {
        return $this->belongsTo(TmArea::class);
    }
}
