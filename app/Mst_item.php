<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mst_item extends Model
{
    /**
     * @var string
     */
    protected $table = 'mst_item';

    /**
     * Validation.
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required',
        'kode' => 'required',
        'harga' => 'required',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_by', 'created_at', 'edited_by', 'updated_at'
    ];

    /**
     * @var array
     */
    protected $guarded = [];
}
