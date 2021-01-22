<?php

namespace App\Http\Controllers\Admin;

use App\Models\CloseContact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class CloseContactController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/closecontact'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $decription = strtoupper($request->decription);
        $arsip = $request->category;

        //Count Data
        $query = CloseContact::with(['user', 'workforce']);
        // $query->whereRaw("upper(decription) like '%$decription%'");
        // if ($arsip) {
        //     $query->onlyTrashed();
        // }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = CloseContact::with(['user', 'workforce']);
        // if ($arsip) {
        //     $query->onlyTrashed();
        // }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $closecontacts = $query->get();

        $data = [];
        foreach ($closecontacts as $closecontact) {
            $closecontact->no = ++$start;
            $data[] = $closecontact;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.closecontact.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd('aaaaaaaa');
        return view('admin.closecontact.create');
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
            'workforce_id'       => 'required',
            'date'               => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'    => $validator->errors()->first()
            ], 400);
        }

        try {
            $close_contact = CloseContact::create([
                'workforce_id'  => $request->workforce_id,
                'date'          => $request->date,
                'description'   => $request->description,
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
            'results'   => route('closecontact.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CloseContact  $closeContact
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $closecontact = CloseContact::find($id);
        if ($closecontact) {
            return view('admin.closecontact.detail', compact('closecontact'));
        } else {
            abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CloseContact  $closeContact
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $closecontact = CloseContact::find($id);
        if ($closecontact) {
            return view('admin.closecontact.edit', compact('closecontact'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CloseContact  $closeContact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'workforce_id'       => 'required',
            'date'               => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'    => $validator->errors()->first()
            ], 400);
        }

        $closecontact = CloseContact::find($id);
        $closecontact->workforce_id = $request->workforce_id;
        $closecontact->date         = $request->date;
        $closecontact->description  = $request->description;
        $closecontact->updated_by   = Auth::id();
        $closecontact->save();

        if (!$closecontact) {
            return response()->json([
                'status'    => false,
                'message'     => $closecontact
            ], 400);
        }

        return response()->json([
            'status'     => true,
            'results'     => route('closecontact.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CloseContact  $closeContact
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $closecontact = CloseContact::find($id);
            $closecontact->forceDelete()();
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
