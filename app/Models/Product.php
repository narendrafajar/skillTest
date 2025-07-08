<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'categories_id',
        'prod_code',
        'prod_name',
        'prod_desc',
        'image_path'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class,'categories_id');
    }
}
