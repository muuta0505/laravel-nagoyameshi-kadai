<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;
    protected $fillable = [ 
        'name',
        'image',
        'description',
        'lowest_price',
        'highest_price',
        'postal_code',
        'address',
        'opening_time',
        'closing_time',
        'seating_capacity',

    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function categories() {
<<<<<<< HEAD
        return $this->belongsToMany(Category::class,'category_restaurant')->withTimestamps();
=======
        return $this->belongsToMany(Category::class)->withTimestamps();
>>>>>>> feature-category-restaurant
    }
}
