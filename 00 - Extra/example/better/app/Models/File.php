<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function givenAnswer()
    {
        return $this->belongsTo(GivenAnswer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSize(): int
    {
        return Storage::disk('local')->size($this->path);
    }
}
