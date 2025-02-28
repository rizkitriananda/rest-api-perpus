<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $guarded = ['id'];

    public function book(){
        return $this->belongsTo(Book::class);
    }
}
