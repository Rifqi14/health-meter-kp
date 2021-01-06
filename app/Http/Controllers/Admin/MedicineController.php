<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class MedicineController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/medicine'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.medicine.index');
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
        $query = Medicine::with('user')->whereRaw("upper(name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Medicine::with('user')->whereRaw("upper(name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicines = $query->get();

        $data = [];
        foreach ($medicines as $medicine) {
            $medicine->no = ++$start;
            $data[] = $medicine;
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
        $query = Medicine::whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Medicine::whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $medicines = $query->get();

        $data = [];
        foreach ($medicines as $medicine) {
            $medicine->no = ++$start;
            $data[] = $medicine;
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
        return view('admin.medicine.create');
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
            'code'              => 'required|unique:medicines|regex:/^.*(?=.*[A-Z]).*$/',
            'name'              => 'required',
            'medicine_category' => 'required',
            'medicine_group'    => 'required',
            'medicine_unit'     => 'required',
            'medicine_type'     => 'required',
            'level'             => 'required',
            'price'             => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        try {
            $medicines = Medicine::create([
                'code'                  => $request->code,
                'name'                  => $request->name,
                'id_medicine_category'  => $request->medicine_category,
                'id_medicine_group'     => $request->medicine_group,
                'id_medicine_unit'      => $request->medicine_unit,
                'id_medicine_type'      => $request->medicine_type,
                'level'                 => $request->level,
                'description'           => $request->description,
                'price'                 => $request->price,
                'updated_by'            => Auth::id()
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('medicine.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function show(Medicine $medicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $medicine = Medicine::withTrashed()->find($id);
        if ($medicine) {
            return view('admin.medicine.edit', compact('medicine'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code'      => 'required|unique:medicines,code,' . $id,
            'name'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $medicine = Medicine::withTrashed()->find($id);
        $medicine->code                 = $request->code;
        $medicine->name                 = $request->name;
        $medicine->id_medicine_category = $request->medicine_category;
        $medicine->id_medicine_group    = $request->medicine_group;
        $medicine->id_medicine_unit     = $request->medicine_unit;
        $medicine->id_medicine_type     = $request->medicine_type;
        $medicine->level                = $request->level;
        $medicine->description          = $request->description;
        $medicine->price                = $request->price;
        $medicine->updated_by           = Auth::id();
        $medicine->save();

        if (!$medicine) {
            return response()->json([
                'status'    => false,
                'message'   => $medicine
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('medicine.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $medicine = Medicine::find($id);
            $medicine->delete();
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

    public function restore(Request $request)
    {
        try {
            $medicine = Medicine::onlyTrashed()->find($request->id);
            $medicine->restore();
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

    public function delete(Request $request)
    {
        try {
            $medicine = Medicine::onlyTrashed()->find($request->id);
            $medicine->forceDelete();
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

    public function import()
    {
        return view('admin.medicine.import');
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'         => 'required|mimes:xlsx'
        ]);
        $file = $request->file('file');
        try {
            $filetype     = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $data   = [];
        $no     = 1;
        $sheet  = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $code = $sheet->getCellByColumnAndRow(0, $row)->getCalculatedValue();
            $name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            if ($code) {
                $data[] = array(
                    'index' => $no,
                    'code' => $code,
                    'name' => $name
                );
                $no++;
            }
        }
        return response()->json([
            'status'     => true,
            'data'     => $data
        ], 200);
    }

    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'medicines'         => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $medicines = json_decode($request->medicines);
        foreach ($medicines as $medicine) {
            $cek = Medicine::whereRaw("upper(code) = '$medicine->code'")->first();
            if (!$cek) {
                $medic = Medicine::create([
                    'code'     => strtoupper($medicine->code),
                    'name'     => $medicine->name
                ]);
            }
        }
        return response()->json([
            'status'     => true,
            'results'    => route('medicine.index'),
        ], 200);
    }
}