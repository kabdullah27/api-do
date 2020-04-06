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
     * @var array
     */
    protected $guarded = [];
}
