<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Mst_item;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Item_controller extends Controller
{
    /**
     * @var
     */
    protected $user;

    /**
     * Item_Controller constructor.
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
        $mst_item = Mst_item::where('is_active', 1)->get();

        return $mst_item;
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
        /* $sequence = DB::select("select NEXTVAL(item_sequance) as seq");
        $sequence = $sequence[0]->seq; */

        $id = DB::select('select UUID() as uuid');
        $data['id'] = $id[0]->uuid;
        // $data['kode'] = 'N' . Carbon::now()->year . str_pad($sequence, 5, '0', STR_PAD_LEFT);
        $data['code'] = $request->code;
        $data['description'] = $request->description;
        $data['pieces'] = (isset($request->satuan)) ? $request->pieces : 'UNIT';
        $data['price'] = $request->price;
        $data['is_edit'] = $request->is_edit;
        $data['created_by'] = $user->user_code;
        $data['edited_by'] = $user->user_code;

        // Validation
        $validator = Validator::make($data, Mst_item::$rules);
        if ($validator->fails()) {
            $validateMsg = [$validator->messages()->toArray()];
            return response()->json([
                'success' => false,
                'message' => 'Item failed to save. ' . $validateMsg,
                'data'    => $data
            ], 500);
        }

        // Insert Master & Detail to Database
        Mst_item::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Item success to save',
            'data'    => $data
        ], 200);
    }

    /**
     * Update Data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mst_item primary key (id)  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = JWTAuth::user();

        $data = $request->all();
        $updated_row = Mst_item::where('kode', $data['kode'])->first();
        if (!$updated_row) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
                'data'    => $data
            ], 500);
        }

        Mst_item::where('kode', $data['kode'])
            ->update([
                'description' => $data['description'],
                'pieces' => (isset($data['pieces'])) ? $data['pieces'] : 'UNIT',
                'price' => $data['price'],
                'is_edit' => (isset($data['is_edit'])) ? $data['is_edit'] : 0,
                'edited_by' => $user->user_code,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Item has been updated',
            'data'    => $data
        ], 200);
    }

    /**
     * Delete Data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mst_item primary key (id)  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = JWTAuth::user();

        $data = $request->all();
        $updated_row = Mst_item::where('kode', $data['kode'])->first();
        if (!$updated_row) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
                'data'    => $data
            ], 500);
        }

        Mst_item::where('kode', $data['kode'])
            ->update([
                'is_active' => 0,
                'edited_by' => $user->user_code,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Item has been nonaktif',
            'data'    => $data
        ], 200);
    }
}
