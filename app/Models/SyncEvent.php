<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'status',
    'message_count',
    'device_id',
    'device_name',
    'device_model',
    'app_version',
    'error_message',
])]
class SyncEvent extends Model
{
}
