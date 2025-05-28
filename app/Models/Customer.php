<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeActive($query) 
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $data): Builder
    {
        return $query->when($data, function($query, $data) {
            $query->where('full_name', 'LIKE', '%' . $data . '%');
        });
    }
}
