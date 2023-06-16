<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BccUser extends Model
{
    use HasFactory;

    public $table = 'bcc_users';
    protected $primaryKey = 'user_id';

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
        'stream',
        'ktm_url',
        'ss_follow_url',
        'ss_poster_url',
        'payment_url',
        'approve_ktm',
        'approve_follow',
        'approve_poster',
        'approve_payment',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUrlAttributePapper($papper_url)
    {
        return config('app.url').Storage::url($papper_url);
    }

    public function getUrlAttributeKtm($ktm_url)
    {
        return config('app.url').Storage::url($ktm_url);
    }

    public function getUrlAttributeFollow($ss_follow_url)
    {
        return config('app.url').Storage::url($ss_follow_url);
    }

    public function getUrlAttributePoster($ss_poster_url)
    {
        return config('app.url').Storage::url($ss_poster_url);
    }
}
