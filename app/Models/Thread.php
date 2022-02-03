<?php

Namespace App\Models;

use Xcholars\Database\Orm\Model;

class Thread extends Model
{
    protected $fillable = [
        'user_id',
        'country',
        'title',
        'link',
        'body',
        'clicks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function drop()
    {
        // array_map(function ($comment)
        // {
        //     $comment->drop();
        //
        // }, $this->comments);

        return $this->delete();
    }



}
