<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentationExhibition extends Model
{
    use HasFactory;

    public $table = 'documentation_exhibitions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'url',
    ];

    public function getUrlAttribute($url)
    {
        return config('app.url').Storage::url($url);
    }
}
