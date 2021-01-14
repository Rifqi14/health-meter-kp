<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AgencySite;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AgencySiteController extends Controller
{
    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];

        //Count Data
        $query = AgencySite::with(['site'])->where('agency_id', $request->agency_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AgencySite::with(['site'])->where('agency_id', $request->agency_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $siteusers = $query->get();

        $data = [];
        foreach($siteusers as $agencysite){
            $agencysite->no = ++$start;
			$data[] = $agencysite;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $id_except = [];
        if($request->agency_id){
            $siteusers = AgencySite::where('agency_id','=',$request->agency_id)
            ->get();
            foreach($siteusers as $siteuser){
                array_push($id_except,$siteuser->site_id);
            }
        }
        //Count Data
        $query = Site::whereRaw("upper(name) like '%$name%'");
        if ($request->agency_id) {
            $query->whereNotIn('id', $id_except);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Site::whereRaw("upper(name) like '%$name%'");
        if ($request->agency_id) {
            $query->whereNotIn('id', $id_except);
        }
        $query->offset($start);
        $query->limit($length);
        $siteusers = $query->get();

        $data = [];
        foreach($siteusers as $siteuser){
            $siteuser->no = ++$start;
			$data[] = $siteuser;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'site_id' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $agencysite = AgencySite::where('agency_id','=',$request->agency_id)
                            ->where('site_id','=',$request->site_id)
                            ->get()
                            ->first();
        if(!$agencysite){
            $agencysite = AgencySite::create([
                'agency_id' => $request->agency_id,
                'site_id' 	=> $request->site_id,
                'updated_by'=> Auth::id()
            ]);
            if (!$agencysite) {
                return response()->json([
                    'status' => false,
                    'message' 	=> $agencysite
                ], 400);
            }
            return response()->json([
                'status' => true,
                'message' 	=> 'Site has been added'
            ], 200);
        }                   
        else{
            return response()->json([
                'status'     => false,
                'message' 	=> 'Existing Site , Select Another'
            ], 400);
        }
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $agencysite = AgencySite::find($id);
            $agencysite->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}