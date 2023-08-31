<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class BccUser extends Model
{
    use HasFactory;
    use Notifiable;

    public $table = 'bcc_users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'team_id',
        'status',
        'papper_url',
        'referral',
        'ktm_url',
        'ss_follow_url',
        'ss_poster_url',
        'payment_url',
        'approve_ktm',
        'approve_follow',
        'approve_poster',
        'approve_payment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getPapperUrlAttribute($url)
    {
        return $url ? config('app.url') . Storage::url($url) : null;
    }

    public function getKtmUrlAttribute($url)
    {
        return config('app.url') . Storage::url($url);
    }

    public function getSsFollowUrlAttribute($url)
    {
        return config('app.url') . Storage::url($url);
    }

    public function getSsPosterUrlAttribute($url)
    {
        return config('app.url') . Storage::url($url);
    }

    public function getPaymentUrlAttribute($url)
    {
        return config('app.url') . Storage::url($url);
    }
}
