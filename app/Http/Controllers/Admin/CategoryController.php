<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'category'));
        $this->middleware('accessmenu');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.category.index');
    }

    public function read(Request $request)
    {
        $parameter = [
            'employee'       => 'Jumlah Personal',
            'subcategory'    => 'Sub Kategori'
        ];

        $type = [
            'summary' => 'Total',
            'filled'  => 'Pengisian'
        ];

        $input = [
            'personil'    => 'Personil',
            'supervisor'  => 'Supervisor'
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('categories');
        $query->select('categories.*');
        $query->whereRaw("upper(categories.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('categories');
        $query->select('categories.*');
        $query->whereRaw("upper(categories.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $categories = $query->get();

        $data = [];
        foreach($categories as $category){
            $category->no = ++$start;
            $category->parameter = $parameter[$category->parameter];
            $category->type = $type[$category->type];
            $category->input = $input[$category->input];
			$data[] = $category;
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

        //Count Data
        $query = DB::table('categories');
        $query->select('categories.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('categories');
        $query->select('categories.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $categories = $query->get();

        $data = [];
        foreach($categories as $category){
            $category->no = ++$start;
			$data[] = $category;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
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
            'name'      => 'required',
            'parameter' => 'required',
            'type' 	    => 'required',
            'input' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $category = Category::create([
            'name' 	    => $request->name,
			'parameter' => $request->parameter,
			'type' 	    => $request->type,
			'input' 	=> $request->input
        ]);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' 	=> $category
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('category.index'),
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
        $parameter = [
            'employee'       => 'Jumlah Personal',
            'subcategory'    => 'Sub Kategori'
        ];

        $type = [
            'summary' => 'Total',
            'filled'  => 'Pengisian'
        ];

        $input = [
            'personil'    => 'Personil',
            'supervisor'  => 'Supervisor'
        ];
        $category = Category::find($id);
        if($category){
            return view('admin.category.detail',compact('category','parameter','type','input'));
        }
        else{
            abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        if($category){
            return view('admin.category.edit',compact('category'));
        }
        else{
            abort(404);
        }
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
            'name' 	    => 'required',
            'parameter' => 'required',
            'type' 	    => 'required',
            'input' 	=> 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $category = Category::find($id);
        $category->name = $request->name;
        $category->parameter = $request->parameter;
        $category->type = $request->type;
        $category->input = $request->input;
        $category->save();

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' 	=> $category
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('category.index'),
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
            $category = Category::find($id);
            $category->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
