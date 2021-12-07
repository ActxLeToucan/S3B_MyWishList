<?php

namespace wishlist\models;

use Illuminate\Database\Eloquent\Model;

class Liste extends Model {

    protected $table = 'Liste';
    protected $primaryKey = 'no';
    public $timestamps = false ;
    protected $fillable = ['no','user_id'];
}
