<?php

Namespace App\Models;

use Xcholars\Database\Orm\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'thread_id',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function reply()
    {
        return $this->hasMany(Reply::class);
    }

    public function drop()
    {
        array_map(function ($reply)
        {
            $reply->drop();

        }, $this->replies);

        return $this->delete();
    }

}
