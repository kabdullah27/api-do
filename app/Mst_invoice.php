<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mst_invoice extends Model
{
    /**
     * @var string
     */
    protected $table = 'mst_invoice';

    /**
     * Validation.
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required',
        'kwitansi_seq' => 'required',
        'inv_seq' => 'required',
        'inv_custid' => 'required',
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
