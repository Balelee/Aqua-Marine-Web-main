<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'contents', 'background_color', 'image', 'name'];

    public function getImageAttribute($image) {
        if (!empty($image)) {
            return url(Storage::url($image));
        }

        return $image;
    }

    // Méthode pour marquer une story comme vue
    public function markAsSeen()
    {
        $this->is_seen = true;
        $this->save();
    }

    // Méthode pour vérifier si une story est vue
    public function isStorySeen()
    {
        return $this->is_seen;
    }
}
