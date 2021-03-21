<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model {
    protected $fillable = ['name'];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function kota()
    {
        return $this->hasMany(MainCategory::class);
    }

    public function kecamatan() {
       return $this->hasMany(SubCategory::class);
    }
}
