<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class UserGroup extends Model
{
    use HasFactory;
        
    // $fillableや$guardedの設定を行う
        protected $fillable = ['user_id', 'group_id', 'owner_flg'];
        
        // リレーションの定義
    public function group()
    {
        return $this->belongsTo(Group::class);  // UserGroupはGroupに属している
    }

    // // Usersとのリレーション（←これがないとエラーになる）
    // public function user()
    // {
    //     // return $this->belongsToMany(User::class, 'user_groups', 'group_id', 'user_id');
    //     return $this->belongsTo(User::class);
    // }

    // public function user_groups()
    // {
    //     return $this->belongsTo(Group::class);  // UserGroupはGroupに属している
    // }
}