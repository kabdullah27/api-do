<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mst_customer extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_by', 'created_at', 'edited_by', 'updated_at'
    ];
}
