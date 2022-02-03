<?php

Namespace App\Models;

use Xcholars\Database\Orm\Model;

class Reply extends Model
{
    protected $fillable = [
        'user_id',
        'comment_id',
        'sub_reply_id',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function drop()
    {
        array_map(function ($reply)
        {
            if (!empty($reply->where('sub_reply_id', $reply->id)))
            {
                $reply->drop();
            }

            $reply->delete();
            
        }, $this->replies);

        return $this->delete();
    }

}
