<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

/** A physical location ("prevádzka") of a tenant. */
class City extends Model
{
    use BelongsToTenant;

    protected $fillable = ['name', 'address'];

    public function workers()
    {
        return $this->hasMany(User::class, 'city_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_city');
    }
}
