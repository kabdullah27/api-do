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
     * @var array
     */
    protected $guarded = [];
}
