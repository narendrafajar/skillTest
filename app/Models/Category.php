<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'cat_code',
        'cat_name'
    ];

    public function manyProd()
    {
        return $this->hasMany(Product::class,'categories_id','id');
    }
}
