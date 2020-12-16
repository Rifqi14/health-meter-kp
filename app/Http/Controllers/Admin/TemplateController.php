<?php

namespace App\Http\Controllers\Admin;

use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'template'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.template.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = Template::select('templates.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Template::select('templates.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $templates = $query->get();

        $data = [];
        foreach($templates as $template){
            $template->no = ++$start;
			$data[] = $template;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.template.create');
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
            'code'          => 'required|unique:templates',
            'name' 	        => 'required|unique:templates',
            'description'   => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $template = Template::create([
            'code'          => $request->code,
            'name'          => $request->name,
            'description'   => $request->description
        ]);
        $contents = [
            'CalculationMode' => 'Interpretation',
            'EngineVersion' => 'EngineV2',
            'Pages' => [
                '0' => [
                    'Border' => ';;2;;;;;solid:Black',
                    'Brush' => 'solid:Transparent',
                    'Ident' => 'StiPage',
                    'Interaction' => [
                        'Ident' => 'StiInteraction',
                    ],
                    'Margins' => [
                        'Bottom' => 1,
                        'Left' => 1,
                        'Right' => 1,
                        'Top' => 1,
                    ],
                    'Name' => $request->code,
                    'PageHeight' => 29.69,
                    'PageWidth' => 21.01,
                    'Watermark' => [
                        'TextBrush' => 'solid:50,0,0,0',
                    ],
                ]
            ],
            'ReportAlias' => $request->code,
            'ReportFile' => $request->code.'.mrt',
            'ReportName' => $request->code,
            'ReportUnit' => 'Centimeters'
        ];
        file_put_contents('reports/'.$request->code.".mrt", json_encode($contents));
        if (!$template) {
            return response()->json([
                'status' => false,
                'message' 	=> $template
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('template.index'),
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
        $template = Template::find($id);
        if($template){
            return view('admin.template.show',compact('template'));
        }
        else{
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
        $template = Template::find($id);
        if($template){
            return view('admin.template.edit',compact('template'));
        }
        else{
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
            'code'  	    => 'required|unique:templates,code,'.$id,
            'name' 	        => 'required|unique:templates,name,'.$id,
            'description'   => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $template = Template::find($id);
        $code = $template->code;
        $template->code = $request->code;
        $template->name = $request->name;
        $template->description = $request->description;
        $template->save();
        if($code != $request->code){
            if(file_exists('reports/'.$code.'.mrt')){
                unlink('reports/'.$code.'.mrt');
            }
            $contents = [
                'CalculationMode' => 'Interpretation',
                'EngineVersion' => 'EngineV2',
                'Pages' => [
                    '0' => [
                        'Border' => ';;2;;;;;solid:Black',
                        'Brush' => 'solid:Transparent',
                        'Ident' => 'StiPage',
                        'Interaction' => [
                            'Ident' => 'StiInteraction',
                        ],
                        'Margins' => [
                            'Bottom' => 1,
                            'Left' => 1,
                            'Right' => 1,
                            'Top' => 1,
                        ],
                        'Name' => $request->code,
                        'PageHeight' => 29.69,
                        'PageWidth' => 21.01,
                        'Watermark' => [
                            'TextBrush' => 'solid:50,0,0,0',
                        ],
                    ]
                ],
                'ReportAlias' => $request->code,
                'ReportFile' => $request->code.'.mrt',
                'ReportName' => $request->code,
                'ReportUnit' => 'Centimeters'
            ];
            file_put_contents('reports/'.$request->code.".mrt", json_encode($contents));
        }
        if (!$template) {
            return response()->json([
                'status' => false,
                'message' 	=> $template
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('template.index'),
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
            $template = Template::find($id);
            $template->delete();
            if($template){
                if(file_exists('reports/'.$template->code.'.mrt')){
                    unlink('reports/'.$template->code.'.mrt');
                }
            }
            
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
