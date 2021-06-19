<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;


class UniqueUserModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "unique_user";
    protected $guarded = ['id'];

    // relation to user
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    } 
}
