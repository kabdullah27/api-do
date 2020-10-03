<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Dtl_invoice;
use App\Mst_invoice;
use App\Dtl_DO;
use App\Mst_DO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Riskihajar\Terbilang\Facades\Terbilang;
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
        setlocale(LC_ALL, "id-ID.UTF-8");
    }

    /**
     * @return mixed
     */
    public function show()
    {
        $mst_inv = DB::table('mst_invoice')
            ->select(
                'kwitansi_seq',
                'inv_seq',
                'inv_date',
                'inv_custid',
                'inv_deskripsi',
                'is_active',
                'created_by',
                'created_at'
            )
            ->where('is_active', '=', 1)
            ->get();

        foreach ($mst_inv as $key => $val) {
            $data_do = DB::table('dtl_invoice')
                ->leftJoin('mst_delivery_order', 'dtl_invoice.do_seq', '=', 'mst_delivery_order.do_seq')
                ->where('inv_seq', $val->inv_seq)
                ->first();
            $data_inv[$key] = $val;
            $data_inv[$key]->inv_date_fmt = Carbon::createFromFormat('Y-m-d', $val->inv_date)->format('d F Y');
            $data_inv[$key]->total_cost = 0;
            $data_inv[$key]->po_seq = $data_do->po_seq;
            $data_inv[$key]->do_seq = $data_do->do_seq;
            $data_inv[$key]->data_cust = DB::table('mst_customer')
                ->select(
                    'kode',
                    'store_name',
                    'store_rgm',
                    'store_address',
                    'store_postal_code',
                    'store_area',
                    'rgm_cug',
                    'store_cug',
                    'store_email'
                )
                ->where('kode', $val->inv_custid)
                ->first();
            $data_inv[$key]->inv_detail = DB::table('dtl_invoice')
                ->leftJoin('mst_item', 'mst_item.kode', '=', 'dtl_invoice.inv_itemid')
                ->select(
                    'kwitansi_seq',
                    'inv_seq',
                    'do_seq',
                    'inv_rownum',
                    'inv_itemid',
                    'deskripsi_barang',
                    'inv_deskripsi',
                    'inv_qty',
                    'inv_cost',
                    'inv_satuan',
                    'dtl_invoice.is_active'
                )
                ->where('inv_seq', $val->inv_seq)
                ->get();

            $total_cost = 0;
            foreach ($data_inv[$key]->inv_detail as $key2 => $val2) {
                $data_inv[$key]->inv_detail[$key2]->total_cost = $data_inv[$key]->inv_detail[$key2]->inv_cost * $data_inv[$key]->inv_detail[$key2]->inv_qty;
                $total_cost += $data_inv[$key]->inv_detail[$key2]->total_cost;
            }
            $data_inv[$key]->total_cost = $total_cost;
        }

        if (!isset($data_inv)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice data not found',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Invoice data found',
            'data'          => $data_inv,
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function invoice_excel(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $mst_inv = DB::table('mst_invoice')
            ->select(
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'kwitansi_seq',
                'inv_seq',
                'inv_date',
                'inv_custid',
                'inv_deskripsi',
                'is_active',
                'created_by',
                'created_at'
            )
            ->where('is_active', 1)
            ->whereDate('inv_date', '>=', $request->date_from)
            ->whereDate('inv_date', '<=', $request->date_to)
            ->get();

        $total_cost_inv = 0;
        foreach ($mst_inv as $key => $val) {
            $data_do = DB::table('dtl_invoice')
                ->leftJoin('mst_delivery_order', 'dtl_invoice.do_seq', 'mst_delivery_order.do_seq')
                ->where('inv_seq', $val->inv_seq)
                ->first();
            $data_inv[$key] = $val;
            $data_inv[$key]->inv_date_fmt =  Carbon::createFromFormat('Y-m-d', $val->inv_date)->format('d F Y');
            $data_inv[$key]->total_cost = 0;
            $data_inv[$key]->po_seq = $data_do->po_seq;
            $data_inv[$key]->do_seq = $data_do->do_seq;
            $data_inv[$key]->data_cust = DB::table('mst_customer')
                ->select(
                    'kode',
                    'store_name',
                    'store_rgm',
                    'store_address',
                    'store_postal_code',
                    'store_area',
                    'rgm_cug',
                    'store_cug',
                    'store_email'
                )
                ->where('kode', $val->inv_custid)
                ->first();
            $data_inv[$key]->inv_detail = DB::table('dtl_invoice')
                ->leftJoin('mst_item', 'mst_item.kode', '=', 'dtl_invoice.inv_itemid')
                ->select(
                    'kwitansi_seq',
                    'inv_seq',
                    'do_seq',
                    'inv_rownum',
                    'inv_itemid',
                    'deskripsi_barang',
                    'inv_deskripsi',
                    'inv_qty',
                    'inv_cost',
                    'inv_satuan',
                    'dtl_invoice.is_active'
                )
                ->where('inv_seq', $val->inv_seq)
                ->get();

            $total_cost = 0;
            foreach ($data_inv[$key]->inv_detail as $key2 => $val2) {
                $data_inv[$key]->inv_detail[$key2]->total_cost = $data_inv[$key]->inv_detail[$key2]->inv_cost * $data_inv[$key]->inv_detail[$key2]->inv_qty;
                $total_cost += $data_inv[$key]->inv_detail[$key2]->total_cost;
            }
            $data_inv[$key]->total_cost = $total_cost;
            $total_cost_inv += $total_cost;
        }

        if (!isset($data_inv)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice data not found',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'success'           => true,
            'message'           => 'Invoice data found',
            'total_cost_inv'    => $total_cost_inv,
            'terbilang'         => Terbilang::make($total_cost_inv, ' rupiah'),
            'data'              => $data_inv,
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
            ->select(
                'kwitansi_seq',
                'inv_seq',
                'inv_date',
                'inv_custid',
                'inv_deskripsi',
                'is_active',
                'created_by',
                'created_at'
            )
            ->where('inv_seq', $request->inv_seq)
            ->first();

        $data_do = DB::table('dtl_invoice')
            ->leftJoin('mst_delivery_order', 'dtl_invoice.do_seq', 'mst_delivery_order.do_seq')
            ->where('inv_seq', $request->inv_seq)
            ->first();

        $mst_inv->inv_date_fmt =  Carbon::createFromFormat('Y-m-d', $mst_inv->inv_date)->format('d F Y');
        $mst_inv->total_cost = 0;
        $mst_inv->user_print = $user->user_code;
        $mst_inv->po_seq = $data_do->po_seq;
        $mst_inv->do_seq = $data_do->do_seq;
        $mst_inv->data_cust = DB::table('mst_customer')
            ->select(
                'kode',
                'store_name',
                'store_rgm',
                'store_address',
                'store_postal_code',
                'store_area',
                'rgm_cug',
                'store_cug',
                'store_email'
            )
            ->where('kode', $mst_inv->inv_custid)
            ->first();
        $mst_inv->inv_detail = DB::table('dtl_invoice')
            ->leftJoin('mst_item', 'mst_item.kode', '=', 'dtl_invoice.inv_itemid')
            ->select(
                'kwitansi_seq',
                'inv_seq',
                'do_seq',
                'inv_rownum',
                'inv_itemid',
                'deskripsi_barang',
                'inv_deskripsi',
                'inv_qty',
                'inv_cost',
                'inv_satuan',
                'dtl_invoice.is_active'
            )
            ->where('inv_seq', $request->inv_seq)
            ->get();

        $total_cost = 0;

        foreach ($mst_inv->inv_detail as $key => $val) {
            $mst_inv->inv_detail[$key]->total_cost = $mst_inv->inv_detail[$key]->inv_cost * $mst_inv->inv_detail[$key]->inv_qty;
            $total_cost += $mst_inv->inv_detail[$key]->total_cost;
        }
        $mst_inv->total_cost = $total_cost;
        $mst_inv->terbilang = Terbilang::make($mst_inv->total_cost, ' rupiah');


        if (!isset($mst_inv)) {
            return response()->json([
                'success' => false,
                'message' => 'Data invoice not found.',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Invoice data found',
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

        $sequance_kwitansi = DB::table('mst_invoice')
            ->selectRaw('count(kwitansi_seq) count_kwitansi, kwitansi_seq ')
            ->whereRaw('kwitansi_seq = (select CAST(max(kwitansi_seq) OVER (PARTITION BY created_at desc) as CHAR) from mst_invoice limit 1)')
            ->groupBy('kwitansi_seq')
            ->first();

        // Get Sequence
        if (isset($sequance_kwitansi)) {
            if ($sequance_kwitansi->count_kwitansi >= 30) {
                $sequance_kwitansi = DB::select("select NEXTVAL(kwitansi_sequance) as seq");
                $sequance_kwitansi = str_pad($sequance_kwitansi[0]->seq, 5, '0', STR_PAD_LEFT) . '-BE-KW-' . $this->numberToRoman(Carbon::now()->month) . '-' . Carbon::now()->year;
            } else {
                $sequance_kwitansi = $sequance_kwitansi->kwitansi_seq;
            }
        } else {
            $sequance_kwitansi = DB::select("select NEXTVAL(kwitansi_sequance) as seq");
            $sequance_kwitansi = str_pad($sequance_kwitansi[0]->seq, 5, '0', STR_PAD_LEFT) . '-BE-KW-' . $this->numberToRoman(Carbon::now()->month) . '-' . Carbon::now()->year;
        }
        $sequence_inv = DB::select("select NEXTVAL(inv_sequance) as seq");
        $sequence_inv = str_pad($sequence_inv[0]->seq, 5, '0', STR_PAD_LEFT) . '-BE-INV-' . $this->numberToRoman(Carbon::now()->month) . '-' . Carbon::now()->year;

        $id = DB::select('select UUID() as uuid');
        $data_mst['id'] = $id[0]->uuid;
        $data_mst['inv_seq'] = $sequence_inv;
        $data_mst['kwitansi_seq'] = $sequance_kwitansi;
        // $data_mst['inv_date'] = $mst_do->do_date;
        $data_mst['inv_date'] = Carbon::now()->format('Y/m/d');
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
                'message' => 'Invoice failed to save',
                'data'    => $validateMsg
            ], 500);
        }

        // Insert Master & Detail to Database
        Mst_invoice::create($data_mst);
        Dtl_invoice::insert($data_dtl);

        return response()->json([
            'success'       => true,
            'message'       => 'Invoice success to save',
            'data_mst'      => $data_mst,
            'data_detail'   => $data_dtl
        ], 200);
    }

    /**
     * @param int $number
     * @return string
     */
    function numberToRoman($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}
