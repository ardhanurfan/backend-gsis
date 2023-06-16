<?php

namespace App\Models;

use App\Traits\CompositeKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GsicUser extends Model
{
    use HasFactory;
    use CompositeKey;

    public $table = 'gsic_users';
    protected $primaryKey = ['user_id', 'team_id'];

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
        return $this->hasOne(User::class, 'id', 'user_id');
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
