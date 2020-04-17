<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dtl_DO extends Model
{
    /**
     * @var string
     */
    protected $table = 'dtl_delivery_order';

    /**
     * Validation.
     *
     * @var array
     */
    public static $rules = [
        'do_itemid' => 'required',
        'do_qty' => 'required',
        'do_cost' => 'required',
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
