<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    public $table = 'exhibitions';
    protected $primaryKey = 'user_id';

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

    public function documentation() {
        return $this->hasMany(DocumentationExhibition::class, 'user_id', 'user_id');
    }
}
