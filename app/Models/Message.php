<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'body',
        'attributes',
        'queue',
        'queue_url',
        'receipt_handle',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->diffForHumans(),
        );
    }
}
