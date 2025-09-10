<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CalendarGroup;

class Calendar extends Model
{
    use HasFactory;

    protected $table = 'calendars'; // テーブル名

    protected $fillable = [
        'event_start_date',
        'event_start_time',
        'event_end_date',
        'event_end_time',
        'user_id',
        'title',
        'content',
        'color', // 追加
    ];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 複数のグループに紐づく
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'calendar_groups', 'calendar_id', 'group_id');
    }

    public function calendar_groups()
{
    return $this->hasMany(CalendarGroup::class, 'calendar_id');
}
}