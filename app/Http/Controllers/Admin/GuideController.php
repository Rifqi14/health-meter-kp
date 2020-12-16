<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Guide;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class GuideController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'guide'));
        $this->middleware('accessmenu', ['except' => ['select','list']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.guide.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = Guide::select('guides.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Guide::select('guides.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $guides = $query->get();

        $data = [];
        foreach($guides as $guide){
            $guide->no = ++$start;
			$data[] = $guide;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function create()
    {
        return view('admin.guide.create');
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
            'name' 	        => 'required',
            'file'   => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $guide = Guide::create([
            'name'          => $request->name,
            'file'   => ''
        ]);

        $file = $request->file('file');
        if($file){
            $filename = 'document.'. $request->file->getClientOriginalExtension();
            $src = 'assets/guide/document/'.$guide->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $guide->file = $src.'/'.$filename;
            $guide->save();
        }

        if (!$guide) {
            return response()->json([
                'status' => false,
                'message' 	=> $guide
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('guide.index'),
        ], 200);
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
        $guide = Guide::findOrFail($id);

        return view('admin.guide.edit', compact('guide'));
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
        $validator = Validator::make($request->all(), [
            'name'  	    => 'required',
            'file'   => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $guide = Guide::find($id);
        $guide->name = $request->name;
        $guide->save();

        $file = $request->file('file');
        if($file){
            $filename = 'document.'. $request->file->getClientOriginalExtension();
            if(file_exists($guide->file)){
                unlink($guide->file);
            }

            $src = 'assets/guide/document/'.$guide->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $guide->file = $src.'/'.$filename;
            $guide->save();
        }

        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('guide.index'),
        ], 200);
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
            $guide = Guide::find($id);
            if(file_exists($guide->file)){
                unlink($guide->file);
            }
            $guide->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     =>  'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }

    public function list()
    {
        $guides = Guide::get();

        return view('admin.guide.list', compact('guides'));
    }
}
