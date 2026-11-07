<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryMessage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'inquiry_id',
        'sender_type',
        'message',
        'created_at',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }
}
