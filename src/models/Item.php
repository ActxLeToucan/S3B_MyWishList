<?php

namespace wishlist\models;

use Illuminate\Database\Eloquent\Model;
class Item extends Model{
    protected $table = 'item';
    protected $primaryKey = 'id' ;
    public $timestamps = false ;
    protected $fillable = ['nom','liste_id'];

    public function liste(){
        return $this->belongsTo('wishlist\models\Liste',"liste_id");
    }
}
