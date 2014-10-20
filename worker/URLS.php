<?php
 
class URLS extends Illuminate\Database\Eloquent\Model {

	protected $table      = 'urls';
    protected $fillable   = array('cola_id','links');
    protected $guarded    = array('id');
    public    $timestamps = false;

}