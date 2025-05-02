<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewComment extends Model
{
    protected $fillable = [
        'review_id',
        'user_id',
        'comment',
        'type',
        'status',
    ];

    public function review()
    {
        return $this->belongsTo(BusinessReview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
