<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Exhibition extends Model
{
    use HasFactory;
    use Notifiable;

    public $table = 'exhibitions';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'category',
        'description',
        'year',
        'size',
        'instagram',
        'twitter',
        'youtube',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function documentation() {
        return $this->hasMany(DocumentationExhibition::class, 'user_id', 'user_id');
    }
}
