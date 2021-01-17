<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'site'));
        $this->middleware('accessmenu', ['except' => ['select', 'set']]);
    }
    /**
     * Display a listing of the resource.
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
        $code = strtoupper($request->code);
        $name = strtoupper($request->name);
        $category = $request->category;

        //Count Data
        $query = Site::select('sites.*');
        $query->select('sites.*');
        $query->whereRaw("upper(code) like '%$code%'");
        $query->whereRaw("upper(name) like '%$name%'");
        if ($category) {
            $query->whereNotNull('deleted_at');
        } else {
            $query->whereNull('deleted_at');
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Site::with('user')->select('sites.*');
        $query->whereRaw("upper(code) like '%$code%'");
        $query->whereRaw("upper(name) like '%$name%'");
        if ($category) {
            $query->whereNotNull('deleted_at');
        } else {
            $query->whereNull('deleted_at');
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $sites = $query->get();

        $data = [];
        foreach ($sites as $site) {
            $site->no = ++$start;
            $data[] = $site;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;

        //Count Data
        $query = Site::whereRaw("upper(name) like '%$name%'");
        if($data_manager){
            $query->where('id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Site::whereRaw("upper(name) like '%$name%'");
        if($data_manager){
            $query->where('id',$site_id);
        }
        $query->orderBy('id', 'asc');
        $query->offset($start);
        $query->limit($length);
        $sites = $query->get();

        $data = [];
        foreach ($sites as $site) {
            $site->no = ++$start;
            $data[] = $site;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    public function index()
    {
        return view('admin.site.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.site.create');
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
            'code'              => 'required|unique:sites',
            'name'              => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $site = Site::create([
            'code'                  => $request->code,
            'name'                  => $request->name,
            'updated_by'            => Auth::id()
        ]);
        if (!$site) {
            return response()->json([
                'status'    => false,
                'message'   => $site
            ], 400);
        }
        $logo = $request->file('logo');
        if ($logo) {
            $path = 'assets/site/';
            $logo->move($path, $site->code . '.' . $logo->getClientOriginalExtension());
            $filename = $path . $site->code . '.' . $logo->getClientOriginalExtension();
            $site->logo = $filename ? $filename : '';
            $site->save();
        }
        return response()->json([
            'status'     => true,
            'results'     => route('site.index'),
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $site = Site::withTrashed()->find($id);
        if ($site) {
            return view('admin.site.edit', compact('site'));
        } else {
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
            'code'              => 'required|unique:sites,code,' . $id,
            'name'              => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $site = Site::find($id);
        //$site->code = $request->code;
        $site->name = $request->name;
        $site->updated_by = Auth::id();
        $site->save();
        if (!$site) {
            return response()->json([
                'status' => false,
                'message'     => $site
            ], 400);
        }
        $logo = $request->file('logo');
        if ($logo) {
            if (file_exists($site->logo)) {
                unlink($site->logo);
            }
            $path = 'assets/site/';
            $logo->move($path, $site->code . '.' . $logo->getClientOriginalExtension());
            $filename = $path . $site->code . '.' . $logo->getClientOriginalExtension();
            $site->logo = $filename ? $filename : '';
            $site->save();
        }

        return response()->json([
            'status'     => true,
            'results'     => route('site.index'),
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
            $site = Site::find($id);
            $site->delete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error archive data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success archive data'
        ], 200);
    }

    public function restore($id)
    {
        try {
            $site = Site::onlyTrashed()->find($id);
            $site->restore();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error restore data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success restore data'
        ], 200);
    }

    public function delete($id)
    {
        try {
            $site = Site::onlyTrashed()->find($id);
            $site->forceDelete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error delete data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }

    public function set(Request $request)
    {
        $request->session()->put('role_id', $request->id);
        return redirect()->back();
    }
}