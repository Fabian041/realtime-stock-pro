<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TmPart extends Model
{
    use HasFactory;

    protected $table = 'tm_parts';

    protected $guarded = ['id'];

    public function checkout()
    {
        return $this->hasMany(TtCheckout::class);
    }

    public function dc()
    {
        return $this->hasMany(DcStock::class);
    }
    
    public function ma()
    {
        return $this->hasMany(MaStock::class);
    }
    
    public function assy()
    {
        return $this->hasMany(AssyStock::class);
    }
}
