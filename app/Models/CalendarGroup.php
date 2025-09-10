<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarGroup extends Model
{
    use HasFactory;

    protected $table = 'calendar_groups'; // テーブル名

    protected $fillable = [
        'calendar_id',
        'group_id'
    ];

    // カレンダーとのリレーション
    public function calendar()
    {
        return $this->belongsTo(Calendar::class, 'calendar_id');
    }

    // グループとのリレーション
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}