<?php

namespace wishlist\models;

use Illuminate\Database\Eloquent\Model;

class Liste extends Model {
    protected $table = 'liste';
    protected $primaryKey = 'no';
    public $timestamps = false ;

    public function items() {
        return $this->hasMany('wishlist\models\Item',"liste_id");
    }

    public function user() {
        return $this->belongsTo('wishlist\models\Authenticate', "user_id");
    }

    public function messages() {
        return $this->hasMany('wishlist\models\Message', 'id_list');
    }
}
