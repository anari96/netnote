<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentDetail extends Model
{
    use HasFactory;

    protected $fillable=[
        "description",
        "document_id",
    ];

    public function file()
    {
        return $this->hasMany(DocumentDetailFile::class);
    }
}
