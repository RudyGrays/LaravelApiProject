<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $fillable = ['file_id', 'user_id', 'type'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userFile()
    {
        return $this->belongsTo(UserFile::class, 'file_id');
    }

    public static function getUsersWithAccessToFile($fileId)
    {
        return static::where('file_id', $fileId)->with('user')->get()->pluck('user');
    }

    public static function getFilesWithAccessToUser($userId)
    {
        return static::where('user_id', $userId)->with('userFile')->get()->pluck('userFile');
    }
}
