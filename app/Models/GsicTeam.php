<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GsicTeam extends Model
{
    use HasFactory;

    public $table = 'gsic_teams';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_name',
        'leader_id',
        'payment_url',
        'status',
        'approve_payment',
    ];

    public function users() {
        return $this->hasMany(GsicUser::class, 'team_id', 'id');
    }

    public function submissions() {
        return $this->hasMany(GsicSubmission::class, 'team_id', 'id');
    }

    public function getUrlAttribute($payment_url)
    {
        return config('app.url').Storage::url($payment_url);
    }
}
