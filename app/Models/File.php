<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'path',
        'extension',
        'size',
        'status',
        'total_sentences',
        'plagiarized_sentences',
        'similarity_percentage',
        'total_images',       
        'indicated_images',  
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function imagePlagiarismReports()
    {
        return $this->hasMany(ImagePlagiarismReport::class);
    }
}