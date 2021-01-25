<?php

namespace App\Http\Controllers\Admin;

use App\Models\ControlCard;
use App\Models\ControlCardLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ControlCardController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/controlcard'));
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
        $query = ControlCard::with([
            'updatedby',
            'authorizedofficial',
            'checkup_examinationevaluation',
            'checkup_examinationevaluationlevel',
            'checkupresult.patient',
            'examinationevaluation',
            'examinationevaluationlevel',
            'guarantor',
            'nid',
            'nidmaker',
            'sitemaker'
        ]);
        // $query->whereRaw("upper(decription) like '%$decription%'");
        // if ($arsip) {
        //     $query->onlyTrashed();
        // }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ControlCard::with([
            'updatedby',
            'authorizedofficial',
            'checkup_examinationevaluation',
            'checkup_examinationevaluationlevel',
            'checkupresult.patient',
            'examinationevaluation',
            'examinationevaluationlevel',
            'guarantor.title',
            'nid',
            'nidmaker',
            'sitemaker'
        ]);
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
        // dd(Auth::user()->workforce->site_id);
        return view('admin.controlcard.index');
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.controlcard.create');
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
            'control_date'                           => 'required',
            'nid'                                    => 'required',
            'checkup_result_id'                      => 'required',
            'checkup_examination_evaluation_id'      => 'required',
            'checkup_examination_evaluation_level_id'=> 'required',
            'examination_evaluation_id'              => 'required',
            'examination_evaluation_level_id'        => 'required',
            'authorized_official_id'                 => 'required',
            'guarantor_id'                           => 'required'
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'    => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $controlcard = ControlCard::create([
            'status'                                 => 0,
            'control_date'                           => $request->control_date,
            'nid'                                    => $request->nid,
            'checkup_result_id'                      => $request->checkup_result_id,
            'checkup_examination_evaluation_id'      => $request->checkup_examination_evaluation_id,
            'checkup_examination_evaluation_level_id'=> $request->checkup_examination_evaluation_level_id,
            'examination_evaluation_id'              => $request->examination_evaluation_id,
            'examination_evaluation_level_id'        => $request->examination_evaluation_level_id,
            'nid_maker'                              => Auth::id(),
            'site_maker_id'                          => Auth::user()->workforce->site_id,
            'description'                            => $request->description,
            'authorized_official_id'                 => $request->authorized_official_id,
            'guarantor_id'                           => $request->guarantor_id,
            'approval_status'                        => 0,
            'card_control_status'                    =>'Draft',
            'updated_by'                             => Auth::id()
        ]);

        if (!$controlcard) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message'     => $controlcard
            ], 400);
        }

        $controlcardlog = ControlCardLog::create([
            'control_card_id'                 => $controlcard->id,
            'nid'                             => Auth::id(),
            'date'                            => Carbon::now(),
            'examination_evaluation_id'       => $request->examination_evaluation_id,
            'examination_evaluation_level_id' => $request->examination_evaluation_level_id,
            'updated_by'                      => Auth::id(),
            'input_status'                    => 'C'
        ]);

        if (!$controlcardlog) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message'     => $controlcardlog
            ], 400);
        }

        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('controlcard.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ControlCard  $controlCard
     * @return \Illuminate\Http\Response
     */
    public function show(ControlCard $controlCard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ControlCard  $controlCard
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $controlcard = ControlCard::find($id);
        if($controlcard)
        {
            return view('admin.controlcard.edit', compact('controlcard'));
        }else{
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ControlCard  $controlCard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'control_date'                           => 'required',
            'nid'                                    => 'required',
            'checkup_result_id'                      => 'required',
            'checkup_examination_evaluation_id'      => 'required',
            'checkup_examination_evaluation_level_id'=> 'required',
            'examination_evaluation_id'              => 'required',
            'examination_evaluation_level_id'        => 'required',
            'authorized_official_id'                 => 'required',
            'guarantor_id'                           => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'    => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();

        $controlcard = ControlCard::find($id);
        $controlcard->status                                  = 0;
        $controlcard->control_date                            = $request->control_date;
        $controlcard->nid                                     = $request->nid;
        $controlcard->checkup_result_id                       = $request->checkup_result_id;
        $controlcard->checkup_examination_evaluation_id       = $request->checkup_examination_evaluation_id;
        $controlcard->checkup_examination_evaluation_level_id = $request->checkup_examination_evaluation_level_id;
        $controlcard->examination_evaluation_id               = $request->examination_evaluation_id;
        $controlcard->examination_evaluation_level_id         = $request->examination_evaluation_level_id;
        $controlcard->nid_maker                               = Auth::id();
        $controlcard->site_maker_id                           = Auth::user()->workforce->site_id;
        $controlcard->description                             = $request->description;
        $controlcard->authorized_official_id                  = $request->authorized_official_id;
        $controlcard->guarantor_id                            = $request->guarantor_id;
        $controlcard->approval_status                         = 0;
        $controlcard->card_control_status                     = 'Draft';
        $controlcard->updated_by                              = Auth::id();
        $controlcard->save();

        if (!$controlcard) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message'     => $controlcard
            ], 400);
        }

        $controlcardlog = ControlCardLog::create([
            'control_card_id'                 => $controlcard->id,
            'nid'                             => Auth::id(),
            'date'                            => Carbon::now(),
            'examination_evaluation_id'       => $request->examination_evaluation_id,
            'examination_evaluation_level_id' => $request->examination_evaluation_level_id,
            'updated_by'                      => Auth::id(),
            'input_status'                    => 'U'
        ]);

        if (!$controlcardlog) {
            DB::rollback();
            return response()->json([
                'status'    => false,
                'message'     => $controlcardlog
            ], 400);
        }

        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('controlcard.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ControlCard  $controlCard
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $controlcard = ControlCard::find($id);
            // dd($controlcard);
            $controlcard->forceDelete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data'.$e->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
