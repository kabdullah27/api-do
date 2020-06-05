<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Mst_customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Customer_controller extends Controller
{
    /**
     * @var
     */
    protected $user;

    /**
     * Customer_Controller constructor.
     */
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * @return mixed
     */
    public function show()
    {
        $mst_customer = Mst_customer::where('is_active', 1)->get();

        return $mst_customer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        //get data user
        $user = JWTAuth::user();

        // Get Sequence
        /* $sequence = DB::select("select NEXTVAL(customer_sequance) as seq");
        $sequence = $sequence[0]->seq; */

        $id = DB::select('select UUID() as uuid');
        $data['id'] = $id[0]->uuid;
        // $data['kode'] = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        $data['kode'] = $request->kode;
        $data['store_name'] = $request->store_name;
        $data['store_rgm'] = $request->store_rgm;
        $data['store_address'] = $request->store_address;
        $data['store_city'] = $request->store_city;
        $data['store_postal_code'] = (isset($request->store_postal_code)) ? $request->store_postal_code : 0;
        $data['store_area'] = $request->store_area;
        $data['rgm_cug'] = $request->rgm_cug;
        $data['store_cug'] = $request->store_cug;
        $data['store_email'] = $request->store_email;
        $data['business_hour'] = (isset($request->business_hour)) ? $request->business_hour : '24 Hour (Mon-Sun)';
        $data['store_status'] = (isset($request->store_status)) ? $request->store_status : 'Open';
        $data['store_category'] = $request->store_category;
        $data['created_by'] = $user->user_code;
        $data['edited_by'] = $user->user_code;

        // Validation
        $validator = Validator::make($data, Mst_customer::$rules);
        if ($validator->fails()) {
            $validateMsg = [$validator->messages()->toArray()];
            return response()->json([
                'success' => false,
                'message' => 'Data Customer Gagal Dibuat. ' . $validateMsg,
                'data'    => $data
            ], 500);
        }

        // Insert Master & Detail to Database
        Mst_customer::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data Customer Berhasil Dibuat',
            'data'    => $data
        ], 200);
    }

    /**
     * Update Data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mst_customer primary key (id)  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = JWTAuth::user();

        $data = $request->all();
        $updated_row = Mst_customer::where('kode', $data['kode'])->first();
        if (!$updated_row) {
            return response()->json([
                'success' => false,
                'message' => 'Data Customer Tidak Ditemukan.',
                'data'    => $data
            ], 500);
        }

        Mst_customer::where('kode', $data['kode'])
            ->update([
                'store_name' => $data['store_name'],
                'store_rgm' => $data['store_rgm'],
                'store_address' => $data['store_address'],
                'store_city' => $data['store_city'],
                'store_postal_code' => (isset($data['store_postal_code'])) ? $data['store_postal_code'] : 0,
                'store_area' => $data['store_area'],
                'rgm_cug' => $data['rgm_cug'],
                'store_cug' => $data['store_cug'],
                'store_email' => $data['store_email'],
                'business_hour' => $data['business_hour'],
                'store_status' => $data['store_status'],
                'store_category' => $data['store_category'],
                'edited_by' => $user->user_code,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Customer Berhasil Di Ubah',
            'data'    => $data
        ], 200);
    }

    /**
     * Check Data Cust.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mst_customer primary key (id)  $id
     * @return \Illuminate\Http\Response
     */
    public function check_cust(Request $request)
    {
        $data = $request->all();
        $updated_row = Mst_customer::where('kode', $data['kode'])->first();
        if (!$updated_row) {
            return response()->json([
                'success' => false,
                'message' => 'Data Customer Belum Digunakan.',
                'data'    => $data
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Customer Sudah Digunakan',
            'data'    => $data
        ], 200);
    }

    /**
     * Delete Data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mst_customer primary key (id)  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = JWTAuth::user();

        $data = $request->all();
        $updated_row = Mst_customer::where('kode', $data['kode'])->first();
        if (!$updated_row) {
            return response()->json([
                'success' => false,
                'message' => 'Data Customer Tidak Ditemukan.',
                'data'    => $data
            ], 500);
        }

        Mst_customer::where('kode', $data['kode'])
            ->update([
                'is_active' => 0,
                'edited_by' => $user->user_code,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Customer Berhasil Di nonaktifkan',
            'data'    => $data
        ], 200);
    }
}
