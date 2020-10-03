<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Dtl_DO;
use App\Mst_DO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Riskihajar\Terbilang\Facades\Terbilang;
use Carbon\Carbon;

class DO_Controller extends Controller
{
    /**
     * @var
     */
    protected $user;

    /**
     * DO_Controller constructor.
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
        $mst_do = DB::table('mst_delivery_order')
            ->select(
                'do_seq',
                'po_seq',
                'do_date',
                'do_custid',
                'do_deskripsi',
                'is_active',
                'created_by',
                'created_at'
            )
            ->where('is_active', 1)
            ->get();

        foreach ($mst_do as $key => $val) {
            $flag_invoice = DB::table('dtl_invoice')
                ->where('do_seq', $val->do_seq)
                ->first();
            $data_do[$key] = $val;
            $data_do[$key]->do_date_fmt = Carbon::createFromFormat('Y-m-d', $val->do_date)->format('d F Y');
            $data_do[$key]->flag_invoice = (isset($flag_invoice)) ? 1 : 0;
            $data_do[$key]->total_cost = 0;
            $data_do[$key]->data_cust = DB::table('mst_customer')
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
                ->where('kode', $val->do_custid)
                ->first();
            $data_do[$key]->do_detail = DB::table('dtl_delivery_order')
                ->leftJoin('mst_item', 'mst_item.kode', '=', 'dtl_delivery_order.do_itemid')
                ->select(
                    'do_seq',
                    'do_rownum',
                    'do_itemid',
                    'deskripsi_barang',
                    'do_deskripsi',
                    'do_qty',
                    'do_cost',
                    'do_satuan',
                    'dtl_delivery_order.is_active'
                )
                ->where('do_seq', $val->do_seq)
                ->get();
            $total_cost = 0;
            foreach ($data_do[$key]->do_detail as $key2 => $val2) {
                $data_do[$key]->do_detail[$key2]->total_cost = $data_do[$key]->do_detail[$key2]->do_cost * $data_do[$key]->do_detail[$key2]->do_qty;
                $total_cost += $data_do[$key]->do_detail[$key2]->total_cost;
            }
            $data_do[$key]->total_cost = $total_cost;
        }

        if (!isset($data_do)) {
            return response()->json([
                'success' => false,
                'message' => 'Data DO not found',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Data DO success to find',
            'data'          => $data_do,
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function prints_do(Request $request)
    {
        $user = JWTAuth::user();
        // dd($request);
        $mst_do = DB::table('mst_delivery_order')
            ->select(
                'do_seq',
                'po_seq',
                'do_date',
                'do_custid',
                'do_deskripsi',
                'is_active',
                'created_by',
                'created_at'
            )
            ->where('do_seq', $request->do_seq)
            ->first();

        $flag_invoice = DB::table('dtl_invoice')
            ->where('do_seq', $request->do_seq)
            ->first();
        $mst_do->flag_invoice = (isset($flag_invoice)) ? 1 : 0;
        $mst_do->do_date_fmt =  Carbon::createFromFormat('Y-m-d', $mst_do->do_date)->format('d F Y');
        $mst_do->total_cost = 0;
        $mst_do->user_print = $user->user_code;
        $mst_do->data_cust = DB::table('mst_customer')
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
            ->where('kode', $mst_do->do_custid)
            ->first();
        $mst_do->do_detail = DB::table('dtl_delivery_order')
            ->leftJoin('mst_item', 'mst_item.kode', '=', 'dtl_delivery_order.do_itemid')
            ->select(
                'do_seq',
                'do_rownum',
                'do_itemid',
                'deskripsi_barang',
                'do_deskripsi',
                'do_qty',
                'do_cost',
                'do_satuan',
                'dtl_delivery_order.is_active'
            )
            ->where('do_seq', $request->do_seq)
            ->get();
        $total_cost = 0;
        foreach ($mst_do->do_detail as $key => $val) {
            $mst_do->do_detail[$key]->total_cost = $mst_do->do_detail[$key]->do_cost * $mst_do->do_detail[$key]->do_qty;
            $total_cost += $mst_do->do_detail[$key]->total_cost;
        }
        $mst_do->total_cost = $total_cost;
        $mst_do->terbilang = Terbilang::make($mst_do->total_cost, ' rupiah');


        if (!isset($mst_do)) {
            return response()->json([
                'success' => false,
                'message' => 'Data DO not found.',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Data DO success to find',
            'data'          => $mst_do,
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

        // Get Sequence
        $sequence = DB::select("select NEXTVAL(do_sequance) as seq");
        $sequence = $sequence[0]->seq;

        $id = DB::select('select UUID() as uuid');
        $data_mst['id'] = $id[0]->uuid;
        $data_mst['do_seq'] = str_pad($sequence, 5, '0', STR_PAD_LEFT) . '-BE-' .  $this->numberToRoman(Carbon::now()->month) . '-' . Carbon::now()->year;
        $data_mst['po_seq'] = $request->po_seq;
        $data_mst['do_date'] = (isset($request->do_date)) ? $request->do_date : Carbon::now()->format('Y/m/d');
        $data_mst['do_custid'] = $request->do_custid;
        $data_mst['do_deskripsi'] = $request->do_deskripsi;
        $data_mst['created_by'] = $user->user_code;
        $data_mst['edited_by'] = $user->user_code;

        // Validation
        $validator = Validator::make($data_mst, Mst_DO::$rules);
        foreach ($request->do_detail as $key => $val) {
            $validatorDtl = Validator::make($val, Dtl_DO::$rules);
        }
        if ($validator->fails() || $validatorDtl->fails()) {
            $validateMsg = [$validatorDtl->messages()->toArray(), $validator->messages()->toArray()];
            return response()->json([
                'success' => false,
                'message' => 'Data DO failed to save',
                'data'    => $validateMsg
            ], 500);
        }

        foreach ($request->do_detail as $key => $val) {
            $data_dtl[$key]['do_seq'] = $data_mst['do_seq'];
            $data_dtl[$key]['do_rownum'] = $key + 1;
            $data_dtl[$key]['do_itemid'] = $val['do_itemid'];
            $data_dtl[$key]['do_deskripsi'] = (isset($val['do_deskripsi'])) ? $val['do_deskripsi'] : NULL;
            $data_dtl[$key]['do_qty'] = $val['do_qty'];
            $data_dtl[$key]['do_cost'] = $val['do_cost'];
            $data_dtl[$key]['do_satuan'] = 'UNIT';
            $data_dtl[$key]['is_active'] = 1;
            $data_dtl[$key]['created_by'] = $user->user_code;
            $data_dtl[$key]['edited_by'] = $user->user_code;
        }
        // Insert Master & Detail to Database
        Mst_DO::create($data_mst);
        Dtl_DO::insert($data_dtl);

        return response()->json([
            'success'       => true,
            'message'       => 'Data DO success to save',
            'data_mst'      => $data_mst,
            'data_detail'   => $data_dtl
        ], 200);
    }

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
