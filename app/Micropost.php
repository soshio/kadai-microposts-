<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    //micropostをお気に入りに追加したユーザーを取得
    public function favorited_users(){
        return $this->belongsToMany('User::class','micropost_user','micropost_id','user_id')->withTimestamps();
    }
}