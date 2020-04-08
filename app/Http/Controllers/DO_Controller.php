<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Dtl_DO;
use App\Mst_DO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
    }

    /**
     * @return mixed
     */
    public function show()
    {
        $mst_do = DB::table('mst_delivery_order')
            ->join('dtl_delivery_order','mst_delivery_order.do_seq','=','dtl_delivery_order.do_seq')
            ->where('mst_delivery_order.is_active', '=', 1)
            ->get();

        return $mst_do;
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
        $data_mst['do_seq'] = str_pad($sequence, 5, '0', STR_PAD_LEFT) . '/BE/DO/' . Carbon::now()->year;
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
                'message' => 'Data DO Gagal Dibuat.',
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
            'message'       => 'Data DO Berhasil Dibuat',
            'data_mst'      => $data_mst,
            'data_detail'   => $data_dtl
        ], 200);
    }
}