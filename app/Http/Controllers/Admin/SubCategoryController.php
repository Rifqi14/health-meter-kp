<?php

namespace App\Http\Controllers\Admin;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{

    public function read(Request $request)
    {
        $type = [
            'range'  => 'Range',
            'yesno'  => 'Yes/No'
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $category_id = $request->category_id;

        //Count Data
        $query = DB::table('sub_categories');
        $query->select('sub_categories.*');
        $query->where('category_id',$category_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('sub_categories');
        $query->select('sub_categories.*');
        $query->where('category_id',$category_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $subcategories = $query->get();

        $data = [];
        foreach($subcategories as $subcategory){
            $subcategory->no = ++$start;
            $subcategory->type = $type[$subcategory->type];
			$data[] = $subcategory;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'   => 'required',
            'name'          => 'required',
            'type'          => 'required',
            'information'   => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $subcategory = SubCategory::create([
            'category_id'   => $request->category_id,
			'name'          => $request->name,
            'type' 	        => $request->type,
            'min'           => $request->type=='range'?$request->min:null,
            'max'           => $request->type=='range'?$request->max:null,
            'information'   => $request->information
        ]);
        if (!$subcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $subcategory
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'message' 	=> 'Success Create Data'
        ], 200);
    }

    public function edit($id){
        $subcategory = SubCategory::find($id);
        return response()->json([
            'status' 	=> true,
            'data' => $subcategory
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'type'          => 'required',
            'information'   => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $subcategory = SubCategory::find($id);
        $subcategory->name = $request->name;
        $subcategory->type = $request->type;
        $subcategory->min  = $request->type=='range'?$request->min:null;
        $subcategory->max  = $request->type=='range'?$request->max:null;
        $subcategory->information  = $request->information;
        $subcategory->save();
        if (!$subcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $subcategory
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'message' 	=> 'Success Update Data'
        ], 200);
    }
    public function destroy($id)
    {
        try {
            $subcategory = SubCategory::find($id);
            $subcategory->delete();
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
