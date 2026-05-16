<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'sender', 'body', 'timestamp', 'direction'])]
class Message extends Model
{
    protected function casts(): array
    {
        return [
            'timestamp' => 'integer',
        ];
    }
}
