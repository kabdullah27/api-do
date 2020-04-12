<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Dtl_invoice;
use App\Mst_invoice;
use App\Dtl_DO;
use App\Mst_DO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Invoice_Controller extends Controller
{
    /**
     * @var
     */
    protected $user;

    /**
     * Invoice_controller constructor.
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
        $mst_inv = DB::table('mst_invoice')
            ->where('is_active', '=', 1)
            ->get();

        foreach ($mst_inv as $key => $val) {
            $data_inv[$key] = $val;
            $data_inv[$key]->inv_detail = DB::table('dtl_invoice')
                ->where('inv_seq', '=', $val->inv_seq)
                ->get();
        }

        if (!isset($data_inv)) {
            return response()->json([
                'success' => false,
                'message' => 'Data Invoice Tidak Ditemukan.',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Data Invoice Ditemukan.',
            'data'          => $data_inv,
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function prints_inv(Request $request)
    {
        $user = JWTAuth::user();
        $mst_inv = DB::table('mst_invoice')
            ->where('inv_seq', $request->inv_seq)
            ->first();

        $mst_inv->user_print = $user->user_code;
        $mst_inv->do_detail = DB::table('dtl_invoice')
            ->where('inv_seq', $request->inv_seq)
            ->get();

        if (!isset($mst_inv)) {
            return response()->json([
                'success' => false,
                'message' => 'Data Invoice Tidak Ditemukan.',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Data Invoice Berhasil Ditemukan.',
            'data'          => $mst_inv,
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $user = JWTAuth::user();

        $mst_do = DB::table('mst_delivery_order')
            ->where('do_seq', '=', $request->do_seq)
            ->first();

        $dtl_do = DB::table('dtl_delivery_order')
            ->where('do_seq', '=', $request->do_seq)
            ->get();

        $sequance_kwitansi = DB::table('dtl_invoice')
            ->where('is_active', '=', 1)
            ->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'))
            ->first();

        // Get Sequence
        if (!isset($sequance_kwitansi)) {
            $sequance_kwitansi = DB::select("select NEXTVAL(kwitansi_sequance) as seq");
            $sequance_kwitansi = str_pad($sequance_kwitansi[0]->seq, 5, '0', STR_PAD_LEFT) . '/BE/KWI/' . Carbon::now()->year;
        } else {
            $sequance_kwitansi = str_pad($sequance_kwitansi->kwitansi_seq, 5, '0', STR_PAD_LEFT) . '/BE/KWI/' . Carbon::now()->year;
        }
        $sequence_inv = DB::select("select NEXTVAL(inv_sequance) as seq");
        $sequence_inv = str_pad($sequence_inv[0]->seq, 5, '0', STR_PAD_LEFT) . '/BE/INV/' . Carbon::now()->year;

        $id = DB::select('select UUID() as uuid');
        $data_mst['id'] = $id[0]->uuid;
        $data_mst['inv_seq'] = $sequence_inv;
        $data_mst['kwitansi_seq'] = $sequance_kwitansi;
        $data_mst['inv_date'] = $mst_do->do_date;
        $data_mst['inv_custid'] = $mst_do->do_custid;
        $data_mst['inv_deskripsi'] = $mst_do->do_deskripsi;
        $data_mst['created_by'] = $user->user_code;
        $data_mst['edited_by'] = $user->user_code;

        foreach ($dtl_do as $key => $val) {
            $data_dtl[$key]['inv_seq'] = $sequence_inv;
            $data_dtl[$key]['kwitansi_seq'] = $sequance_kwitansi;
            $data_dtl[$key]['do_seq'] = $request->do_seq;
            $data_dtl[$key]['inv_rownum'] = $key + 1;
            $data_dtl[$key]['inv_itemid'] = $val->do_itemid;
            $data_dtl[$key]['inv_deskripsi'] = $val->do_deskripsi;
            $data_dtl[$key]['inv_qty'] = $val->do_qty;
            $data_dtl[$key]['inv_cost'] = $val->do_cost;
            $data_dtl[$key]['inv_satuan'] = $val->do_satuan;
            $data_dtl[$key]['is_active'] = 1;
            $data_dtl[$key]['created_by'] = $user->user_code;
            $data_dtl[$key]['edited_by'] = $user->user_code;
        }

        // Validation
        $validator = Validator::make($data_mst, Mst_invoice::$rules);
        foreach ($data_dtl as $key => $val) {
            $validatorDtl = Validator::make($val, Dtl_invoice::$rules);
        }
        if ($validator->fails() || $validatorDtl->fails()) {
            $validateMsg = [$validatorDtl->messages()->toArray(), $validator->messages()->toArray()];
            return response()->json([
                'success' => false,
                'message' => 'Data Invoice Gagal Dibuat.',
                'data'    => $validateMsg
            ], 500);
        }

        // Insert Master & Detail to Database
        Mst_invoice::create($data_mst);
        Dtl_invoice::insert($data_dtl);

        return response()->json([
            'success'       => true,
            'message'       => 'Data Invoice Berhasil Dibuat',
            'data_mst'      => $data_mst,
            'data_detail'   => $data_dtl
        ], 200);
    }
}
