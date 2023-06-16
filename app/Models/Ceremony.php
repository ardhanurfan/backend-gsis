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

    public function getUrlAttributePoster($ss_poster_url)
    {
        return config('app.url').Storage::url($ss_poster_url);
    }
}
