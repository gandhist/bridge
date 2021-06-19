<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ResponseFormatter;

use Auth;
use Validator;
use Carbon\Carbon;
use App\Models\UniqueUserModel;

class UniqueUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = UniqueUserModel::with('user')->get();
        return ResponseFormatter::success($data, 'Data Found!', 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        return ResponseFormatter::success([], 'this uri is underconstruction!', 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $data = UniqueUserModel::with('user')->find($id);
        return ResponseFormatter::success($data, 'Data Found!', 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return ResponseFormatter::success([], 'this uri is underconstruction!', 400);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $data = UniqueUserModel::find($id);
        $validated = Validator::make($request->all(),[
            'status' => 'numeric|min:0|max:1',
        ]);
        if($validated->fails()){
            return ResponseFormatter::success($validated->errors()->all(), 'unprocessable entity', 422);
        }
        $status = $request->status == 0 ? 'active' : 'inactive';
        $data->status = $request->status != null ? $status : null;
        $data->position = $request->position;
        $data->updated_by = Auth::id();
        $data->updated_at = Carbon::now()->toDateTimeString();
        $data->save();
        return ResponseFormatter::success([], 'Data updated successfully!', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $data = UniqueUserModel::with('user')->find($id);
        $data->deleted_by = Auth::id();
        $data->deleted_at = Carbon::now()->toDateTimeString();
        $data->save();
        return ResponseFormatter::success([], 'Data deleted successfully!', 200);
    }
}
