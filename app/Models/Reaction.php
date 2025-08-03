<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'type'];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
