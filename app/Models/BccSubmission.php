<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BccSubmission extends Model
{
    use HasFactory;

    public $table = 'bcc_submissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'url',
        'round',
    ];

    public function getUrlAttribute($url)
    {
        return config('app.url').Storage::url($url);
    }
}
