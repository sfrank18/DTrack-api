<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Designation extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = [];

    protected $fillable = [
        'name',
        'created_by'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

}
