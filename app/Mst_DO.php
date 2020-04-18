<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mst_DO extends Model
{
    /**
     * @var string
     */
    protected $table = 'mst_delivery_order';

    /**
     * Validation.
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required',
        'do_seq' => 'required',
        'po_seq' => 'required',
        'do_custid' => 'required',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'created_by', 'created_at', 'edited_by', 'updated_at'
    ];


    /**
     * @var array
     */
    protected $guarded = [];
}
