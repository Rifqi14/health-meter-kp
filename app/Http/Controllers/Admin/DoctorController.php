<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\SiteUser;
use App\Role;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class DoctorController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'doctor'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.doctor.index');
    }
    public function read(Request $request)
    {
        $start          = $request->start;
        $length         = $request->length;
        $query          = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir            = $request->order[0]['dir'];
        $name           = strtoupper($request->name);
        $site           = $request->site;

        // Count Data
        $doctors        = Doctor::where('name', 'like', "%$name%");
        if ($site) {
            $query->where('site_id', $site);
        }
        $recordsTotal   = $doctors->count();

        // Select Pagination
        $doctor         = Doctor::where('name', 'like', "%$name%");
        if ($site) {
            $query->where('site_id', $site);
        }
        $doctor->offset($start);
        $doctor->limit($length);
        $doctor->orderBy($sort, $dir);
        $doctors = $doctor->get();

        $data = [];
        foreach ($doctors as $doctor) {
            $doctor->no = ++$start;
            $data[]     = $doctor;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.doctor.create');
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
            'phone'     => 'required',
            'unit'      => 'required',
            'email'     => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'        => false,
                'message'       => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $doctor = Doctor::create([
            'name'      => $request->name,
            'id_doctor' => $request->id_doctor,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'site_id'   => $request->unit,
            'status'    => $request->status ? 1 : 0
        ]);
        if (!$doctor) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $doctor
            ], 400);
        }
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'username'  => $request->id_doctor,
            'password'  => Hash::make($request->password),
            'status'    => 1,
        ]);
        if (!$user) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message'   => $user
            ], 400);
        }
        $role = Role::find($request->role_id);
        $user->attachRole($role);

        $siteuser = SiteUser::create([
            'user_id'       => $user->id,
            'site_id'       => $request->unit
        ]);
        if (!$siteuser) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message'   => $siteuser
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'        => true,
            'results'       => route('doctor.index'),
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
        $doctor = Doctor::find($id);
        if ($doctor) {
            return view('admin.doctor.edit', compact('doctor'));
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
            'id_doctor'     => 'required',
            'name'          => 'required',
            'phone'         => 'required',
            'unit'          => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'        => false,
                'message'       => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $doctor = Doctor::find($id);
        if ($doctor->id_doctor != $request->id_doctor) {
            $user = User::where('username', $doctor->id_doctor)->first();
            $user->username = $request->id_doctor;
            $user->save();
            if (!$user) {
                DB::rollback();
                return response()->json([
                    'status'    => false,
                    'message'   => $user
                ], 400);
            }
        }
        $doctor->id_doctor  = $request->id_doctor;
        $doctor->name       = $request->name;
        $doctor->phone      = $request->phone;
        $doctor->site_id    = $request->unit;
        $doctor->status     = $request->status ? 1 : 0;
        $doctor->save();
        if (!$doctor) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message'   => $doctor
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'        => true,
            'results'       => route('doctor.index')
        ], 200);
    }

    public function import()
    {
        return view('admin.doctor.import');
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'      => 'required|mimes:xlsx'
        ]);
        $file = $request->file('file');
        try {
            $filetype = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $th) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $th->getMessage());
        }
        $data   = [];
        $no     = 1;
        $sheet  = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $id_doctor = strtoupper($sheet->getCellByColumnAndRow(0, $row)->getValue());
            $name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $phone = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $email = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $role_name = strtoupper($sheet->getCellByColumnAndRow(4, $row)->getValue());
            $role = Role::whereRaw("upper(name) = '$role_name'")->first();
            $doctor = Doctor::where('id_doctor', $id_doctor)->first();
            if (!$doctor && $role) {
                $data[] = array(
                    'index'     => $no,
                    'id_doctor' => $id_doctor,
                    'name'      => $name,
                    'phone'     => $phone,
                    'email'     => $email,
                    'role_id'   => $role ? $role->id : 0,
                );
                $no++;
            }
        }
        return response()->json([
            'status'    => true,
            'data'      => $data
        ], 200);
    }

    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctors'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $doctors = json_decode($request->doctors);

        DB::beginTransaction();
        foreach ($doctors as $doctor) {
            $doctor = Doctor::create([
                'id_doctor'     => $doctor->id_doctor,
                'name'          => $doctor->name,
                'phone'         => $doctor->phone,
                'email'         => $doctor->email,
                'status'        => 1,
                'site_id'       => $request->session()->get('site_id') ? $request->session()->get('site_id') : 1
            ]);
            if (!$doctor) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $doctor
                ]);
            }
            $user = User::create([
                'name'     => $doctor->name,
                'email'     => $doctor->email,
                'username'     => $doctor->id_doctor,
                'password'    => Hash::make(123456),
                'status'     => 1,
            ]);
            if (!$user) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $user
                ], 400);
            }
            $role = Role::find($doctor->role_id);
            $user->attachRole($role);

            $siteuser = SiteUser::create([
                'user_id' => $user->id,
                'site_id'     => $request->session()->get('site_id') ? $request->session()->get('site_id') : 1
            ]);
            if (!$siteuser) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $siteuser
                ], 400);
            }
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('doctor.index'),
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
            $doctor = Doctor::find($id);
            $user = User::where('username', $doctor->id_doctor)->first();
            if ($user) {
                $user->delete();
            }
            $doctor->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }
}