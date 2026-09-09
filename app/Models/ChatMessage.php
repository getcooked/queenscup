<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    public const AUTHOR_CUSTOMER = 'customer';
    public const AUTHOR_BOT = 'bot';

    /** Nothing older than this is kept, so a chat cannot grow without limit. */
    public const KEEP_PER_USER = 200;

    protected $fillable = ['user_id', 'author', 'body', 'quick_replies'];

    protected $casts = [
        'quick_replies' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
