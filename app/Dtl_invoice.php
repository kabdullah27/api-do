<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dtl_invoice extends Model
{
    /**
     * @var string
     */
    protected $table = 'dtl_invoice';

    /**
     * Validation.
     *
     * @var array
     */
    public static $rules = [
        'do_seq' => 'required',
        'inv_itemid' => 'required',
        'inv_qty' => 'required',
        'inv_cost' => 'required',
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
