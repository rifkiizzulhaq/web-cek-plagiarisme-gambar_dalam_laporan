<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagePlagiarismReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_id',
        'source_image_index',
        'source_image',
        'match_image',
        'match_doc_title',
        'similarity',
    ];
}
