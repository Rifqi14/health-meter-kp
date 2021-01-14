<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AuthorizedOfficial;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AuthorizedOfficialController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/authorizedofficial'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.authorizedofficial.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $arsip = $request->category;

        //Count Data
        $query = AuthorizedOfficial::with(['user', 'site'])->whereRaw("upper(authority) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AuthorizedOfficial::with(['user', 'site'])->whereRaw("upper(authority) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $data[] = $result;
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

        //Count Data
        $query = AuthorizedOfficial::whereRaw("upper(authority) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AuthorizedOfficial::whereRaw("upper(authority) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $data[] = $result;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.authorizedofficial.create');
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
            'code'     => 'required|unique:authorized_officials',
            'site_id'  => 'required',
            'level'    => 'required',
            'authority'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        try {
            $authority = AuthorizedOfficial::create([
                'code'          => $request->code,
                'site_id'       => $request->site_id,
                'authority_type'=> $request->authority_type ? 1 : 0,
                'level'         => $request->level,
                'authority'     => $request->authority,
                'updated_by'    => Auth::id(),
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('authorizedofficial.index'),
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
        $authority = AuthorizedOfficial::withTrashed()->find($id);
        if ($authority) {
            return view('admin.authorizedofficial.edit', compact('authority'));
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
            'code'     => 'required|unique:authorized_officials,code,' . $id,
            'site_id'  => 'required',
            'level'    => 'required',
            'authority'=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $authority = AuthorizedOfficial::withTrashed()->find($id);
        $authority->code            = $request->code;
        $authority->site_id         = $request->site_id;
        $authority->authority_type  = $request->authority_type ? 1 : 0;
        $authority->level           = $request->level;
        $authority->authority       = $request->authority;
        $authority->updated_by      = Auth::id();
        $authority->save();
        if (!$authority) {
            return response()->json([
                'status' => false,
                'message' 	=> $authority
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results'   => route('authorizedofficial.index')
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
            $authorizedofficial = AuthorizedOfficial::find($id);
            $authorizedofficial->delete();
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
            $authorizedofficial = AuthorizedOfficial::onlyTrashed()->find($id);
            $authorizedofficial->restore();
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
            $authorizedofficial = AuthorizedOfficial::onlyTrashed()->find($id);
            $authorizedofficial->forceDelete();
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
}