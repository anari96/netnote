<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentDetailFile extends Model
{
    use HasFactory;

    protected $fillable=[
        "path",
        "filename",
        "document_detail_id",
        "cloud_path"
    ];

}
