<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AssessmentAnswer;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AssessmentAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.assessmentanwer.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $question_id = $request->assesment_question_id;
        $arsip = $request->archive;

        //Count Data
        $query = AssessmentAnswer::with(['user'])->where('assessment_question_id', $question_id);
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AssessmentAnswer::with(['user'])->where('assessment_question_id', $question_id);
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
        $question = $request->question_id;

        //Count Data
        $query = AssessmentAnswer::whereRaw("upper(description) like '%$name%'");
        if($question){
            $query->where('assessment_question_id', $question);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AssessmentAnswer::with('question')->whereRaw("upper(description) like '%$name%'");
        if($question){
            $query->where('assessment_question_id', $question);
        }
        $query->offset($start*$length);
        $query->limit($length);
        $query->orderBy('assessment_question_id','asc');
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            if($result->question){
                $data[] = $result;
            }
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
        //
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
            'assessment_question_id'    => 'required',
            'description'               => 'required',
            'rating'                    => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        try {
            $answer = AssessmentAnswer::create([
                'assessment_question_id'    => $request->assessment_question_id,
                'answer_type'               => '',
                'description'               => $request->description,
                'rating'                    => $request->rating,
                'information'               => $request->information,
                'updated_by'                => Auth::id()
            ]);
        } catch (QueryException $th) {
            return response()->json([
        		'status' 	=> false,
        		'message' 	=> 'Error create data : ' . $th->errorInfo[2]
        	], 400);
        }
        return response()->json([
            'status' 	=> true,
            'message' 	=> 'Success Create Data'
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
        $answer = AssessmentAnswer::withTrashed()->find($id);
        return response()->json([
            'status'    => true,
            'data'      => $answer
        ], 200);
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
            'assessment_question_id'    => 'required',
            'description'               => 'required',
            'rating'                    => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $answer = AssessmentAnswer::withTrashed()->find($id);
        $answer->assessment_question_id = $request->assessment_question_id;
        $answer->description            = $request->description;
        $answer->rating                 = $request->rating;
        $answer->information            = $request->information;
        $answer->updated_by             = Auth::id();
        $answer->save();
        if (!$answer) {
            return response()->json([
                'status'    => false,
                'message' 	=> $answer
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success Update Data'
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
            $delete = AssessmentAnswer::find($id);
            $delete->delete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error archive data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'        => true,
            'message'       => 'Success archive data'
        ], 200);
    }

    public function restore(Request $request)
    {
        try {
            $restore = AssessmentAnswer::withTrashed()->find($request->id);
            $restore->restore();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error restore data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'        => true,
            'message'       => 'Success restore data'
        ], 200);
    }

    public function delete(Request $request)
    {
        try {
            $delete = AssessmentAnswer::withTrashed()->find($request->id);
            $delete->forceDelete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error delete data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'        => true,
            'message'       => 'Success delete data'
        ], 200);
    }
}