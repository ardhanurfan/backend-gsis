<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ceremony extends Model
{
    use HasFactory;

    public $table = 'ceremonies';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ss_poster_url',
        'approve_poster',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getSsPosterUrlAttribute($url)
    {
        return config('app.url').Storage::url($url);
    }
}
