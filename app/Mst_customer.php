<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mst_customer extends Model
{
    /**
     * @var string
     */
    protected $table = 'mst_customer';

    /**
     * Validation.
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required',
        'kode' => 'required',
        'store_name' => 'required',
        'store_rgm' => 'required',
        'store_address' => 'required',
        'store_city' => 'required',
        'store_area' => 'required',
        'rgm_cug' => 'required',
        'store_cug' => 'required',
        'store_email' => 'required',
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
