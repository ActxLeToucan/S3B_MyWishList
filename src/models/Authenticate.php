<?php

namespace wishlist\models;

use Illuminate\Database\Eloquent\Model;

class Authenticate extends Model{
    protected $table = 'connexion';
    protected $primaryKey = 'id' ;
    public $timestamps = false ;

    public function lists() {
        return $this->hasMany('wishlist\models\Liste', "user_id");
    }
}