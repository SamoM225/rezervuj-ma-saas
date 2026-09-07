<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use BelongsToTenant, HasFactory, HasTranslations;

    /**
     * Attributes that can be translated
     */
    protected array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'city',
        'name_translations',
        'description',
        'description_translations',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'description_translations' => 'array',
    ];

    /** Services listed under this category (a service belongs to exactly one). */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function cities()
    {
        return $this->belongsToMany(City::class, 'category_city');
    }
}
