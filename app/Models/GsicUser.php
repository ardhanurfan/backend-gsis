<?php

namespace App\Models;

use App\Traits\CompositeKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GsicUser extends Model
{
    use HasFactory;

    public $table = 'gsic_users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ktm_url',
        'ss_follow_url',
        'ss_poster_url',
        'approve_ktm',
        'approve_follow',
        'approve_poster',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getKtmUrlAttribute($url)
    {
        return config('app.url').Storage::url($url);
    }

    public function getSsFollowUrlAttribute($url)
    {
        return config('app.url').Storage::url($url);
    }

    public function getSsPosterUrlAttribute($url)
    {
        return config('app.url').Storage::url($url);
    }
}
