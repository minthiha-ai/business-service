<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'rating',
        'review',
    ];

    public function business()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
