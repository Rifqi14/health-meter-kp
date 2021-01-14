<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Role;
use App\Models\Employee;
use App\Models\Checkup;
use App\Models\CheckupDetail;
use App\Models\Title;
use App\Models\Medical;
use App\Models\MedicalDetail;
use App\Models\Region;
use App\Models\Report;
use App\Models\EmployeeMovement;
use App\Models\SiteUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class EmployeeController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employee'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.employee.index');
    }
    public function read(Request $request)
    {
        $type = [
            'permanent'   => 'Pegawai Tetap',
            'internship'  => 'Alih Daya',
            'pensiun'  => 'Pensiun',
            'other'  => 'Lainya',
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        $category = $request->category;

        //Count Data
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
        if ($category) {
            $query->whereNotNull('deleted_at');
        } else {
            $query->whereNull('deleted_at');
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select('employees.*', 'titles.name as title_name', 'regions.name as place_of_birth', 'sites.name as site_name');
        $query->leftJoin('employee_movements', 'employee_movements.employee_id', '=', 'employees.id');
        $query->leftJoin('titles', 'titles.id', '=', 'employee_movements.title_id');
        $query->leftJoin('regions', 'regions.id', '=', 'employees.place_of_birth');
        $query->leftJoin('sites', 'sites.id', '=', 'employees.site_id');
        $query->whereNull('finish');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if ($site) {
            $query->where('employees.site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
        if ($category) {
            $query->whereNotNull('deleted_at');
        } else {
            $query->whereNull('deleted_at');
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            $employee->type = $type[$employee->type];
            $employee->birth_date = Carbon::parse($employee->birth_date)->format('d F Y');
            $data[] = $employee;
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
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->whereNull('deleted_at');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->whereNull('deleted_at');
        $query->offset($start);
        $query->limit($length);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            $data[] = $employee;
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
        return view('admin.employee.create');
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
            'nid'      => 'required|unique:employees',
            'email'  => 'required|unique:employees',
            'name'   => 'required',
            'title_id'  => 'required',
            'type'   => 'required',
            'gender'   => 'required',
            'place_of_birth'   => 'required',
            'birth_date'   => 'required',
            'phone'   => 'required',
            'address'   => 'required',
            'password'   => 'required',
            'unit'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $employee = Employee::create([
            'nid'  => $request->nid,
            'email'  => $request->email,
            'name' => $request->name,
            'type' => $request->type,
            'gender' => $request->gender,
            'place_of_birth' => $request->place_of_birth,
            'birth_date' => $request->birth_date,
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'site_id'   => $request->unit
        ]);
        if (!$employee) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'     => $employee
            ], 400);
        }
        $employeemovement = EmployeeMovement::create([
            'employee_id' => $employee->id,
            'title_id'    => $request->title_id,
            'start'       => date('Y-m-d H:i:s'),
            'finish'      => null,
            'reason'      => 'Pembuatan Jabatan',
            'status'      => 1
        ]);
        if (!$employeemovement) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'     => $employeemovement
            ], 400);
        }
        $user = User::create([
            'name'     => $request->name,
            'email'     => $request->email,
            'username'     => $request->nid,
            'password'    => Hash::make($request->password),
            'status'     => 1,
            'employee_id'     => $employee->id,
        ]);
        if (!$user) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'     => $user
            ], 400);
        }
        $role = Role::find($request->role_id);
        $user->attachRole($role);

        $siteuser = SiteUser::create([
            'user_id' => $user->id,
            'site_id'     => $request->unit
        ]);
        if (!$siteuser) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'     => $siteuser
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('employee.index'),
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
        $type = [
            'permanent'   => 'Pegawai Tetap',
            'internship'  => 'Alih Daya',
            'pensiun'  => 'Pensiun',
            'other'  => 'Lainya',
        ];
        $employee = Employee::with(['region', 'movement' => function ($q) {
            $q->with(['title' => function ($q) {
                $q->with('grade');
            }])->whereNull('finish')->first();
        }])
            ->select('employees.*')
            ->find($id);
        if ($employee) {
            $employee->type = $type[$employee->type];
            return view('admin.employee.detail', compact('employee'));
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
        $employee = Employee::with(['region', 'movement' => function ($q) {
            $q->with(['title' => function ($q) {
                $q->with('grade');
            }])->whereNull('finish')->first();
        }])
            ->select('employees.*')
            ->find($id);
        if ($employee) {
            return view('admin.employee.edit', compact('employee'));
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
            'title_id'       => 'required',
            'nid'              => 'required',
            'name'           => 'required',
            'type'           => 'required',
            'gender'         => 'required',
            'place_of_birth' => 'required',
            'birth_date'     => 'required',
            'phone'          => 'required',
            'address'        => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'unit'           => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $employee = Employee::with('region')
            ->select('employees.*', 'employee_movements.title_id', 'titles.name as title_name')
            ->leftJoin('employee_movements', 'employee_movements.employee_id', '=', 'employees.id')
            ->leftJoin('titles', 'titles.id', '=', 'employee_movements.title_id')
            ->whereNull('finish')
            ->find($id);
        if ($employee->nid != $request->nid) {
            $user = User::where('username', $employee->nid)->first();
            $user->username = $request->nid;
            $user->save();
            if (!$user) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $user
                ], 400);
            }
        }
        if ($employee->title_id != $request->title_id) {
            $employeemovement = EmployeeMovement::where('employee_id', $employee->id)
                ->where('title_id', $employee->title_id)
                ->whereNull('finish')
                ->first();
            $employeemovement->finish = date('Y-m-d H:i:s');
            $employeemovement->save();
            if (!$employeemovement) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $employeemovement
                ], 400);
            }
            $employeemovement = EmployeeMovement::create([
                'employee_id' => $employee->id,
                'title_id'    => $request->title_id,
                'start'       => date('Y-m-d H:i:s'),
                'finish'      => null,
                'reason'      => 'Manual Update Jabatan',
                'status'      => 1
            ]);
            if (!$employeemovement) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $employeemovement
                ], 400);
            }
        }
        $employee->nid            = $request->nid;
        $employee->name           = $request->name;
        $employee->type           = $request->type;
        $employee->gender         = $request->gender;
        $employee->place_of_birth = $request->place_of_birth;
        $employee->birth_date     = $request->birth_date;
        $employee->phone          = $request->phone;
        $employee->address        = $request->address;
        $employee->latitude       = $request->latitude;
        $employee->longitude      = $request->longitude;
        $employee->site_id        = $request->unit;
        $employee->save();
        if (!$employee) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'     => $employee
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('employee.index'),
        ], 200);
    }

    public function import()
    {
        return view('admin.employee.import');
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
        $data     = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $nid = strtoupper($sheet->getCellByColumnAndRow(0, $row)->getValue());
            $name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $title_name = strtoupper($sheet->getCellByColumnAndRow(2, $row)->getValue());
            $type = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $gender = $sheet->getCellByColumnAndRow(4, $row)->getValue();
            $place_of_birth = $sheet->getCellByColumnAndRow(5, $row)->getValue();
            $birth_date = $sheet->getCellByColumnAndRow(6, $row)->getCalculatedValue();
            $phone = $sheet->getCellByColumnAndRow(7, $row)->getValue();
            $address = $sheet->getCellByColumnAndRow(8, $row)->getValue();
            $role_name = strtoupper($sheet->getCellByColumnAndRow(9, $row)->getValue());
            $email = $sheet->getCellByColumnAndRow(10, $row)->getValue();
            $title = Title::whereRaw("upper(name) = '$title_name'")->first();
            $employee = Employee::whereRaw("upper(nid) = '$nid'")->first();
            $role = Role::whereRaw("upper(name) = '$role_name'")->first();
            $region = Region::whereRaw("upper(name) = '$place_of_birth'")->first();
            if ($title && !$employee && $role) {
                $data[] = array(
                    'index' => $no,
                    'title_id' => $title ? $title->id : 0,
                    'role_id' => $role ? $role->id : 0,
                    'title_name' => $title ? $title->name : '',
                    'department_name' => $title ? $title->department->name : '',
                    'name' => $name,
                    'nid' => $nid,
                    'type' => $type,
                    'place_of_birth' => $region ? $region->id : 0,
                    'birth_date' => $birth_date,
                    'birth_date' => $birth_date,
                    'gender' => $gender == 'PEREMPUAN' ? 'female' : 'male',
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
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
            'employees'         => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $employees = json_decode($request->employees);

        DB::beginTransaction();
        foreach ($employees as $data) {
            $employee = Employee::create([
                'nid'  => $data->nid,
                'email'  => $data->email,
                'name' => $data->name,
                'type' => $data->type,
                'gender' => $data->gender == 'LAKI-LAKI' ? 'm' : 'f',
                'place_of_birth' => $data->place_of_birth ? $data->place_of_birth : 133,
                'birth_date' => $data->birth_date,
                'phone' => $data->phone ? $data->phone : 0,
                'address' => $data->address,
                'latitude' => 0,
                'longitude' => 0
            ]);
            if (!$employee) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $employee
                ], 400);
            }
            $employeemovement = EmployeeMovement::create([
                'employee_id' => $employee->id,
                'title_id'    => $data->title_id,
                'start'       => date('Y-m-d H:i:s'),
                'finish'      => null,
                'reason'      => 'Pembuatan Jabatan',
                'status'      => 1
            ]);
            if (!$employeemovement) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $employeemovement
                ], 400);
            }
            $user = User::create([
                'name'     => $data->name,
                'email'     => $data->email,
                'username'     => $data->nid,
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
            $role = Role::find($data->role_id);
            $user->attachRole($role);

            $siteuser = SiteUser::create([
                'user_id' => $user->id,
                'site_id'     => $request->session()->get('site_id')
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
            'status'     => true,
            'results'     => route('employee.index'),
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
            $employee = Employee::find($id);
            $employee->delete();
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
            $employee = Employee::onlyTrashed()->find($id);
            $employee->restore();
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
            $employee = Employee::onlyTrashed()->find($id);
            $employee->forceDelete();
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

    public function medis(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('checkup_details');
        $query->select('employees.*');
        $query->leftJoin('checkups', 'checkups.id', '=', 'checkup_details.checkup_id');
        $query->leftJoin('medical_details', 'medical_details.id', '=', 'checkup_details.medical_detail_id');
        $query->where("checkups.employee_id", $employee_id);
        $query->where("checkup_details.value", '<>', 'Tidak');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('checkup_details');
        $query->select('checkup_details.*', 'checkups.code', 'medical_details.name');
        $query->leftJoin('checkups', 'checkups.id', '=', 'checkup_details.checkup_id');
        $query->leftJoin('medical_details', 'medical_details.id', '=', 'checkup_details.medical_detail_id');
        $query->where("checkups.employee_id", $employee_id);
        $query->where("checkup_details.value", '<>', 'Tidak');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            $data[] = $employee;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    public function history(Request $request)
    {
        $employee_id = $request->employee_id;
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        // Count Data
        $query = Report::select('reports.report_date', DB::raw("max(case when sub_categories.name = 'Suhu Badan' then value else 0 end) as suhu_badan"), DB::raw("max(case when sub_categories.name = 'Apakah Sehat?' then value else 0 end) as sehat"), DB::raw("max(case when sub_categories.name = 'Saturasi Oksigen' then value else 0 end) as saturasi"), DB::raw("max(case when sub_categories.name = 'Hasil Profil Resiko Covid-19 termasuk tinggi' then value else 0 end) as resiko"));
        $query->leftJoin('sub_categories', 'sub_categories.id', '=', 'reports.sub_category_id');
        $query->where("reports.employee_id", $employee_id);
        $query->groupBy('reports.report_date');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Report::select('reports.report_date', DB::raw("max(case when sub_categories.name = 'Suhu Badan' then value else 0 end) as suhu_badan"), DB::raw("max(case when sub_categories.name = 'Apakah Sehat?' then value else 0 end) as sehat"), DB::raw("max(case when sub_categories.name = 'Saturasi Oksigen' then value else 0 end) as saturasi"), DB::raw("max(case when sub_categories.name = 'Hasil Profil Resiko Covid-19 termasuk tinggi' then value else 0 end) as resiko"));
        $query->leftJoin('sub_categories', 'sub_categories.id', '=', 'reports.sub_category_id');
        $query->where("reports.employee_id", $employee_id);
        $query->groupBy('reports.report_date');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $reports = $query->get();
        $data = [];
        foreach ($reports as $report) {
            $report->no = ++$start;
            $report->sehat = $report->sehat ? 'Ya' : 'Tidak';
            $report->suhu_badan = $report->suhu_badan . ' °C';
            $report->saturasi = $report->saturasi . ' %';
            $report->resiko = $report->resiko ? 'Ya' : 'Tidak';
            $data[] = $report;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function temperature(Request $request)
    {
        $employee_id = $request->employee_id;
        $start = date('Y-m-d', strtotime(date('Y-m-d') . ' -6 day'));
        $finish = date('Y-m-d');
        $query = Report::select('reports.report_date', 'reports.value');
        $query->leftJoin('sub_categories', 'sub_categories.id', '=', 'reports.sub_category_id');
        $query->whereBetween('reports.report_date', [$start, $finish]);
        $query->where('sub_categories.name', 'Suhu Badan');
        $query->where('reports.employee_id', $employee_id);
        $query->orderBy('report_date', 'asc');
        $reports = $query->get();
        $series = [];
        $categories = [];
        foreach ($reports as $report) {
            $categories[] = $report->report_date;
            $series[] = intval($report->value);
        }
        return response()->json([
            'date' => date('d/m/Y', strtotime(date('Y-m-d') . ' -6 day')) . ' - ' . date('d/m/Y'),
            'series' => $series,
            'categories' => $categories
        ], 200);
    }

    public function saturasi(Request $request)
    {
        $employee_id = $request->employee_id;
        $start = date('Y-m-d', strtotime(date('Y-m-d') . ' -6 day'));
        $finish = date('Y-m-d');
        $query = Report::select('reports.report_date', 'reports.value');
        $query->leftJoin('sub_categories', 'sub_categories.id', '=', 'reports.sub_category_id');
        $query->whereBetween('reports.report_date', [$start, $finish]);
        $query->where('sub_categories.name', 'Saturasi Oksigen');
        $query->where('reports.employee_id', $employee_id);
        $query->orderBy('report_date', 'asc');
        $reports = $query->get();
        $series = [];
        $categories = [];
        foreach ($reports as $report) {
            $categories[] = $report->report_date;
            $series[] = intval($report->value);
        }
        return response()->json([
            'date' => date('d/m/Y', strtotime(date('Y-m-d') . ' -6 day')) . ' - ' . date('d/m/Y'),
            'series' => $series,
            'categories' => $categories
        ], 200);
    }

    public function exportmedis(Request $request)
    {
        $i = 0;
        $types = [
            'history' => 'Riwayat',
            'laboratory' => 'Laboraturium',
            'nonlaboratury' => 'Non Laboraturium',
            'physical' => 'Fisik'
        ];
        $employee_id = $request->employee_id;
        $checkups = Checkup::where('employee_id', $employee_id)->get();

        $object     = new \PHPExcel();
        $object->getProperties()->setCreator('PT PJB UNIT PEMBANGKITAN GRESIK');
        foreach ($types as $key => $value) {
            $medicals = Medical::orderBy('id', 'asc')->where('type', $key)->get();
            $medicaldetails = MedicalDetail::select('medical_details.*')
                ->leftJoin('medicals', 'medicals.id', '=', 'medical_details.medical_id')
                ->where('type', $key)
                ->orderBy('id', 'asc')->get();
            if ($i > 0) {
                $object->createSheet();
                $object->setActiveSheetIndex($i);
                $sheet = $object->getActiveSheet();
                $sheet->setTitle($value);
            } else {
                $object->setActiveSheetIndex(0);
                $sheet = $object->getActiveSheet();
                $sheet->setTitle($value);
            }
            $sheet->setCellValue('A1', 'No Dokumen');
            $sheet->setCellValue('B1', 'NID');
            $sheet->setCellValue('C1', 'Nama');
            $sheet->setCellValue('D1', 'Tanggal');
            $column = 4;
            foreach ($medicals as $medical) {
                $sheet->setCellValueByColumnAndRow($column, 1, $medical->name);
                $start = \PHPExcel_Cell::stringFromColumnIndex($column);
                foreach ($medicaldetails as $medicaldetail) {
                    if ($medical->id == $medicaldetail->medical_id) {
                        $sheet->setCellValueByColumnAndRow($column, 2, $medicaldetail->name);
                        $column++;
                    }
                }
                $end = \PHPExcel_Cell::stringFromColumnIndex($column - 1);
                $counter = 1;
                $merge = "$start{$counter}:$end{$counter}";
                $object->getActiveSheet()->getStyle("$start{$counter}:$end{$counter}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f4ff81');
                $object->getActiveSheet()->mergeCells($merge);
            }
            $object->getActiveSheet()->mergeCells('A1:A2');
            $object->getActiveSheet()->mergeCells('B1:B2');
            $object->getActiveSheet()->mergeCells('C1:C2');
            $object->getActiveSheet()->mergeCells('D1:D2');
            $row_number = 3;
            $no = 1;
            foreach ($checkups as $checkup) {
                $sheet->setCellValue('A' . $row_number, $checkup->code);
                $sheet->setCellValue('B' . $row_number, $checkup->employee->nid);
                $sheet->setCellValue('C' . $row_number, $checkup->employee->name);
                $sheet->setCellValue('D' . $row_number, "'" . $checkup->checkup_date);
                $column = 4;
                foreach ($medicals as $medical) {
                    foreach ($medicaldetails as $medicaldetail) {
                        if ($medical->id == $medicaldetail->medical_id) {
                            $checkupvalue = CheckupDetail::where('medical_detail_id', $medicaldetail->id)
                                ->where('checkup_id', $checkup->id)
                                ->first();
                            $sheet->setCellValueByColumnAndRow($column, $row_number, $checkupvalue->value);
                            $column++;
                        }
                    }
                }


                $row_number++;
            }

            foreach (range('A', $object->getActiveSheet()->getHighestColumn()) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $object->getActiveSheet()->freezePane('E3');
            $i++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        return response()->json([
            'status'     => true,
            'name'        => 'data-checkup-' . date('d-m-Y') . '.xlsx',
            'message'    => "Berhasil Download Data Checkup",
            'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
        ], 200);
    }
}