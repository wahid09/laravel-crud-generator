<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [];

    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    protected $casts = [];

    protected $dates = [];

    protected $appends = [];
}
