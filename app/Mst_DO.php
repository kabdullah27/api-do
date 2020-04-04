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
        'do_custid' => 'required',
    ];


    /**
     * @var array
     */
    protected $guarded = [];
}
