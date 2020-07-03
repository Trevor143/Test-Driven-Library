<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $guarded = [];

    protected $dates = ['checked_out_at', 'checked_in_at'];

    public function book()
    {
        $this->belongsTo(Book::class);
    }
}
