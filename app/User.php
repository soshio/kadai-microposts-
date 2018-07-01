<?php

namespace App;

//use App\Micropost;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
     public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
public function follow($userId)
{
    // 既にフォローしているかの確認
    $exist = $this->is_following($userId);
    // 自分自身ではないかの確認
    $its_me = $this->id == $userId;

    if ($exist || $its_me) {
        // 既にフォローしていれば何もしない
        return false;
    } else {
        // 未フォローであればフォローする
        $this->followings()->attach($userId);
        return true;
    }
}

public function unfollow($userId)
{
    // 既にフォローしているかの確認
    $exist = $this->is_following($userId);
    // 自分自身ではないかの確認
    $its_me = $this->id == $userId;

    if ($exist && !$its_me) {
        // 既にフォローしていればフォローを外す
        $this->followings()->detach($userId);
        return true;
    } else {
        // 未フォローであれば何もしない
        return false;
    }
}

public function is_following($userId) {
    return $this->followings()->where('follow_id', $userId)->exists();
}

     //自分と自分がフォローしているユーザーの投稿を取得する
 public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();//あるユーザーがフォローしているユーザーのidを配列で取得
        $follow_user_ids[] = $this->id;//自分のidも配列に加える
        return Micropost::whereIn('user_id', $follow_user_ids);//自分と自分のフォローしたユーザーの投稿だけを返す
    }
    
    //お気に入りの投稿を取得する
    public function favorite_microposts(){
        return $this->belongsToMany(Micropost::class,'micropost_user','user_id','micropost_id')->withTimestamps();
    }
    
    
    
    
    
      //投稿をお気に入りにする
    public function favorite($micropostId)
{
    // 既にお気に入りしているかの確認
    $exist = $this->is_favorite($micropostId);
   
    if ($exist) {
        // 既にお気に入りしていれば何もしない
        return false;
    } else {
        // 未お気に入りであればお気に入りする
        $this->favorite_microposts()->attach($micropostId);
        return true;
    }
}


    //お気に入りしている投稿からお気に入りを削除する
public function unfavorite($micropostId)
{
    // 既にお気に入りしているかの確認
    $exist = $this->is_favorite($micropostId);
    if ($exist) {
        // 既にフォローしていればフォローを外す
       $this->favorite_microposts()->detach($micropostId);
        return true;
    } else {
        // 未フォローであれば何もしない
        return false;
    }
}

 // 既にお気に入りしているかを確認するためのメソッド
public function is_favorite($micropostId) {
    return $this->favorite_microposts()->where('micropost_id', $micropostId)->exists();
    
}
   
   }
   
