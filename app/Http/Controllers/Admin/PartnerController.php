<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'partner'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.partner.index');
    }

    public function read(Request $request)
    {
        $category = [
            'drugstore'      => 'Apotek',
            'hospital'    => 'Rumah Sakit',
            'laboratorium'    => 'Laboratorium'
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $site = $request->site;

        //Count Data
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->whereRaw("upper(partners.name) like '%$name%'");
        if ($site) {
            $query->where('site_id', $site);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->whereRaw("upper(partners.name) like '%$name%'");
        if ($site) {
            $query->where('site_id', $site);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $partners = $query->get();

        $data = [];
        foreach ($partners as $partner) {
            $partner->no = ++$start;
            $partner->category = $category[$partner->category];
            $data[] = $partner;
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
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $partners = $query->get();

        $data = [];
        foreach ($partners as $partner) {
            $partner->no = ++$start;
            $data[] = $partner;
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
        return view('admin.partner.create');
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
            'name'   => 'required',
            'category'   => 'required',
            'phone'   => 'required',
            'address'   => 'required',
            'site'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $partner = Partner::create([
            'name' => $request->name,
            'category' => $request->category,
            'phone' => $request->phone,
            'email'  => $request->email,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'site_id'   => $request->site
        ]);

        return response()->json([
            'status'     => true,
            'results'     => route('partner.index'),
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
        $category = [
            'drugstore'      => 'Apotek',
            'hospital'    => 'Rumah Sakit',
            'laboratorium'    => 'Laboratorium'
        ];
        $partner = Partner::find($id);
        if ($partner) {
            $partner->category = $category[$partner->category];
            return view('admin.partner.detail', compact('partner'));
        } else {
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
        $partner = Partner::find($id);
        if ($partner) {
            return view('admin.partner.edit', compact('partner'));
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
            'name'   => 'required',
            'category'   => 'required',
            'phone'   => 'required',
            'address'   => 'required',
            'site'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $partner = Partner::find($id);
        $partner->name = $request->name;
        $partner->category = $request->category;
        $partner->phone = $request->phone;
        $partner->email = $request->email;
        $partner->address = $request->address;
        $partner->latitude  = $request->latitude;
        $partner->longitude  = $request->longitude;
        $partner->site_id = $request->site;
        $partner->save();

        if (!$partner) {
            return response()->json([
                'status' => false,
                'message'     => $partner
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('partner.index'),
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
            $partner = Partner::find($id);
            $partner->delete();
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