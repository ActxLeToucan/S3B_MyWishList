<?php

namespace wishlist\models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    protected $table = 'message';
    protected $primaryKey = 'id_msg';
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('wishlist\models\Authenticate', "id_user");
    }

    public function list() {
        return $this->belongsTo('wishlist\models\Liste', "id_list");
    }
}
