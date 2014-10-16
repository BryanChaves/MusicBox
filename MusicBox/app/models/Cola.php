<?php

class Cola extends Eloquent
{
	protected $table = 'cola';
	protected $fillable = array('file', 'parts','time_per_chunk');
	protected $guarded  = array('id');
	public    $timestamps = false;
}

