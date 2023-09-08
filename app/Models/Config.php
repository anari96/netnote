<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "json_config",
        "auth_name"
    ];

    public function getConfigAttribute(){
        return json_decode($this->json_config);
    }
}
