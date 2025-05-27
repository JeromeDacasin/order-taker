<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->user()->name;
            $model->user_id   = auth()->id();
        });

        static::updating(function ($model) {
            unset($model->created_by);
            unset($model->user_id);
        });
    }
}
