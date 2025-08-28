<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'subscription_id',
        'type',
        'stripe_subscription_id',
        'start_date',
        'end_date',
        'status',
        'created_at'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
