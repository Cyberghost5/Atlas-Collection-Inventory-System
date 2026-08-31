<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function getNameAttribute($value): string
    {
        if (empty($value)) return '';
        return ucwords(mb_strtolower($value), " \t\r\n\f\v-/(.'\"");
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = ucwords(mb_strtolower(trim($value ?? '')), " \t\r\n\f\v-/(.'\"");
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
