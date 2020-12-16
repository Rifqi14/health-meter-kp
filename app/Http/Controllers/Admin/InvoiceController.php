<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\InvoiceDocument;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\MedicalRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'invoice'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.invoice.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $partner_id = $request->partner_id;
        $status = strtoupper($request->status);
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;

        //Count Data
        $query = DB::table('invoices');
        $query->select('invoices.*');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->whereBetween('invoice_date', [$date_start, $date_finish]);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('invoices');
        $query->select('invoices.*','partners.name as partner_name');
        $query->leftJoin('partners','invoices.partner_id','=','partners.id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->whereBetween('invoice_date', [$date_start, $date_finish]);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $invoices = $query->get();

        $data = [];
        foreach($invoices as $invoice){
            $invoice->no = ++$start;
			$data[] = $invoice;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function itemread(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $invoice_id = $request->invoice_id;

        // Count Data
        $query = DB::table('invoice_items');
        $query->select('invoice_items.id.*');
        $query->leftJoin('medical_records', 'medical_records.id', '=', 'invoice_items.medical_record_id');
        $query->leftJoin('employees', 'medical_records.employee_id', '=', 'employees.id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('partners', 'medical_records.partner_id', '=', 'partners.id');
        $query->where('invoice_items.invoice_id', $invoice_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('invoice_items');
        $query->select('invoice_items.id',
                        'medical_records.record_no',
                        'medical_records.date',
                        'invoice_items.created_at',
                        'medical_actions.name as medical_action_name',
                        'employees.name as employee_name',
                        'partners.name as partner_name');
        $query->leftJoin('medical_records', 'medical_records.id', '=', 'invoice_items.medical_record_id');
        $query->leftJoin('employees', 'medical_records.employee_id', '=', 'employees.id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('partners', 'medical_records.partner_id', '=', 'partners.id');
        $query->where('invoice_items.invoice_id', $invoice_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $invoiceitems = $query->get();
        // dd($invoiceitems);
        $data = [];
        foreach($invoiceitems as $invoiceitem){
            $invoiceitem->no = ++$start;
			$data[] = $invoiceitem;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function coverread(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $partner_id = $request->partner_id;

        // Count Data
        $query = MedicalRecord::select('medical_records.*',
        'employees.name as employee_name',
        'medical_actions.name as medical_action_name','partners.name as partner_name');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('partners', 'medical_records.partner_id', '=', 'partners.id');
        $query->where('medical_records.partner_id', $partner_id);
        $query->where('medical_records.status', 'Closed');
        $query->whereNull('medical_records.invoice_id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = MedicalRecord::select('medical_records.*',
        'employees.name as employee_name',
        'medical_actions.name as medical_action_name','partners.name as partner_name');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('partners', 'medical_records.partner_id', '=', 'partners.id');
        $query->where('medical_records.partner_id', $partner_id);
        $query->where('medical_records.status', 'Closed');
        $query->whereNull('medical_records.invoice_id');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicalrecords = $query->get();
        $data = [];
        foreach($medicalrecords as $medicalrecord){
            $medicalrecord->no = ++$start;
			$data[] = $medicalrecord;
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
        $documents = Document::all();
        return view('admin.invoice.create',compact('documents'));
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
            'invoice_date'  => 'required',
            'partner_id'      => 'required',
            'status'  => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $invoice = Invoice::create([
            'invoice_date'  => $request->invoice_date,
            'receive_date'  => $request->receive_date,
            'close_date'  => $request->close_date,
            'partner_id' 	=> $request->partner_id,
            'status' 	    => $request->status

        ]);
        if (!$invoice) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $invoice
            ], 400);
        }
        if($request->invoice_document){
            foreach($request->invoice_document as $key => $value){
                $invoicedocument = InvoiceDocument::create([
                    'document_id' => $value,
                    'invoice_id' => $invoice->id,
                    'status' => isset($request->document_status[$value])?1:0,
                    'notes' => $request->notes[$value]?$request->notes[$value]:''
                ]);
                if (!$invoicedocument) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' 	=> $invoicedocument
                    ], 400);
                }
            }
        }
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('invoice.index'),
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
        $invoice = Invoice::find($id);
        // dd($invoice);
        if($invoice){

            return view('admin.invoice.detail',compact('invoice'));
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
        $documents = Document::all();
        $invoice = Invoice::with('partner')->find($id);
        if($invoice){
            foreach($documents as $document){
                $invoicedocument = InvoiceDocument::where('invoice_id',$id)
                                                    ->where('document_id',$document->id)
                                                    ->first();
                if(!$invoicedocument){
                    $invoicedocument = InvoiceDocument::create([
                        'document_id' => $document->id,
                        'invoice_id' => $id,
                        'status' => 0,
                        'notes' =>''
                    ]);
                }
            }
            return view('admin.invoice.edit',compact('invoice','documents'));
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
            'partner_id'  => 'required',
            'invoice_date'      => 'required',
            'status'  => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $invoice = Invoice::find($id);
        $invoice->partner_id = $request->partner_id;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->receive_date = $request->receive_date;
        $invoice->close_date = $request->close_date;
        $invoice->status = $request->status;
        $invoice->save();

        if (!$invoice) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $invoice
            ], 400);
        }
        if($request->invoice_document){
            foreach($request->invoice_document as $key => $value){
                $invoicedocument = InvoiceDocument::where('invoice_id',$id)
                                                    ->where('document_id',$value)
                                                    ->first();
                if(!$invoicedocument){
                    $invoicedocument = InvoiceDocument::create([
                        'document_id' => $value,
                        'invoice_id' => $invoice->id,
                        'status' => isset($request->document_status[$value])?1:0,
                        'notes' => $request->notes[$value]?$request->notes[$value]:''
                    ]);
                    if (!$invoicedocument) {
                        DB::rollback();
                        return response()->json([
                            'status' => false,
                            'message' 	=> $invoicedocument
                        ], 400);
                    }
                }
                else{
                    $invoicedocument->status = isset($request->document_status[$value])?1:0;
                    $invoicedocument->notes = $request->notes[$value]?$request->notes[$value]:'';
                    $invoicedocument->save();
                    if (!$invoicedocument) {
                        DB::rollback();
                        return response()->json([
                            'status' => false,
                            'message' 	=> $invoicedocument
                        ], 400);
                    }
                }
            }
        }
        $invoiceitems = InvoiceItem::where('invoice_id',$invoice->id)->get();
        foreach($invoiceitems as $invoiceitem){
            $medicalrecord = MedicalRecord::find($invoiceitem->medical_record_id);
            if($invoice->status == 'Progress'){
                $medicalrecord->status_invoice   = 1;
                $medicalrecord->save();
            }else if($invoice->status == 'Closed'){
                $medicalrecord->status_invoice   = 2;
                $medicalrecord->save();
            }else if($invoice->status == 'Request'){
                $medicalrecord->status_invoice   = 0;
                $medicalrecord->save();
            }
        }
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('invoice.index'),
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
            $invoice = Invoice::find($id);
            $invoice->delete();
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

    public function import($id)
    {
        $invoice_id = $id;
        return view('admin.invoice.import', compact('invoice_id'));
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' 	    => 'required|mimes:xlsx'
        ]);
        $file = $request->file('file');
        try {
            $filetype 	= \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch(\Exception $e) {
            die('Error loading file "'.pathinfo($file,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        $data 	= [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++){
            $medical_record = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $recordno = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $date = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $name = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $pasien = $sheet->getCellByColumnAndRow(4, $row)->getValue();
            $partner = $sheet->getCellByColumnAndRow(5, $row)->getValue();
            $medical_action = $sheet->getCellByColumnAndRow(6, $row)->getValue();
            $status = $sheet->getCellByColumnAndRow(7, $row)->getValue();
            $created_at = $sheet->getCellByColumnAndRow(8, $row)->getValue();
            // $medical_record_id = MedicalRecord::whereRaw("id = '$medical_record'")->first();
            if($medical_record){
                $data[] = array(
                    'index'=>$no,
                    'medical_record'=>$medical_record,
                    'date' => $date,
                    'name' => $name,
                    'pasien' => $pasien,
                    'partner' => $partner,
                    'medical_action' => $medical_action,
                    'status' => $status,
                    'created_at' => $created_at,
                );
                $no++;
            }
        }
        return response()->json([
            'status' 	=> true,
            'data' 	=> $data
        ], 200);
    }

    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoices' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $invoice = Invoice::find($request->invoice_id);
        $partner_id = $invoice->partner_id;
        $invoices = json_decode($request->invoices);
        // dd($request->invoice_id);
        foreach($invoices as $invoice){
                $medicalrecord = MedicalRecord::find($invoice->medical_record);
                if($partner_id == $medicalrecord->partner_id){
                    $invoiceitem = InvoiceItem::create([
                        'invoice_id' => $request->invoice_id,
                        'medical_record_id' => $invoice->medical_record,
                    ]);
                    if($invoice->status == 'Progress'){
                        $medicalrecord->status_invoice   = 1;
                        $medicalrecord->invoice_id   = $request->invoice_id;
                        $medicalrecord->save();
                    }else if($invoice->status == 'Closed'){
                        $medicalrecord->status_invoice   = 2;
                        $medicalrecord->invoice_id   = $request->invoice_id;
                        $medicalrecord->save();
                    }
                }

        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('invoice.show',['id'=>$request->invoice_id]),
        ], 200);
    }

    public function coverstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id'  => 'required',
            'medical_record_id'  => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        foreach($request->medical_record_id as $item => $v){
            $invoiceitem = InvoiceItem::create([
                'invoice_id'=>$request->invoice_id,
                'medical_record_id'=>$v,
                ]);
            // dd($invoiceitem);

            if(!$invoiceitem){
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' 	=> $invoiceitem
                ], 400);
            }

            // dd($invoiceitem->invoice->status);

            if($invoiceitem->invoice->status == 'Request' || $invoiceitem->invoice->status == 'Progress'){
                $medicalrecord = MedicalRecord::find($v);
                $medicalrecord->status_invoice   = 1;
                $medicalrecord->invoice_id   = $request->invoice_id;
                $medicalrecord->save();
            }else if($invoiceitem->invoice->status == 'Closed'){
                $medicalrecord = MedicalRecord::find($v);
                $medicalrecord->status_invoice   = 2;
                $medicalrecord->invoice_id   = $request->invoice_id;
                $medicalrecord->save();
            }

            if(!$medicalrecord){
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' 	=> $medicalrecord
                ], 400);
            }

        }

        DB::commit();
        return response()->json([
        	'status' 	=> true,
            'message'   => 'Success add data',
        ], 200);
    }

    public function coverdestroy($id)
    {
        try {
            $invoiceitem = InvoiceItem::find($id);
            $invoice_id = $invoiceitem->invoice_id;
            MedicalRecord::where('invoice_id',$invoice_id)->update([
                'invoice_id'=>null,
                'status_invoice'=>0
            ]);
            $invoiceitem->delete();
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

    public function total(Request $request)
    {
        $partner_id = $request->partner_id;
        $status = strtoupper($request->status);
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $query = Invoice::select('id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->whereBetween('invoice_date', [$date_start, $date_finish]);
        $total = $query->count();
        return $total;
    }
    public function closed(Request $request)
    {
		$partner_id = $request->partner_id;
        $status = strtoupper($request->status);
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $query = Invoice::select('id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->where('status','Closed');
        $query->whereBetween('invoice_date', [$date_start, $date_finish]);
        $total = $query->count();
        return $total;
    }
    public function request(Request $request)
    {
        $partner_id = $request->partner_id;
        $status = strtoupper($request->status);
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $query = Invoice::select('id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->where('status','Request');
        $query->whereBetween('invoice_date', [$date_start, $date_finish]);
        $total = $query->count();
        return $total;
    }

    public function chart(Request $request)
    {
        $partner_id = $request->partner_id;
        $status = strtoupper($request->status);
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $query = Invoice::select('partners.name',DB::raw('count(partners.id) as total'));
        $query->leftJoin('partners','partners.id','=','invoices.partner_id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->whereBetween('invoice_date', [$date_start, $date_finish]);
        $query->orderBy('total','desc');
        $query->limit(10);
        $query->groupBy('partners.name');
        $medicalactions = $query->get();
        $series = [];
		$categories = [];
        foreach($medicalactions as $medicalaction){
            $categories[] = $medicalaction->name;
			$series[] = intval($medicalaction->total);
        }
        return response()->json([
            'title' =>  Carbon::parse($request->date_start)->format('d/m/Y').' - '.Carbon::parse($request->date_finish)->format('d/m/Y'),
			'series' => $series,
			'categories' => $categories
        ], 200);
    }
    public function export(Request $request){
        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('Perki Surabaya');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $partner_id = $request->partner_id;
        $status = strtoupper($request->status);
        // $order_date = $request->order_date;
        $order_date = explode(' - ',$request->order_date);
        $date_start = date('Y-m-d',strtotime(str_replace('/','-',$order_date[0])));
        $date_finish = date('Y-m-d',strtotime(str_replace('/','-',$order_date[1])));
        
        $query = DB::table('invoices');
        $query->select('invoices.*');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->whereBetween('invoice_date', [$date_start, $date_finish]);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('invoices');
        $query->select('invoices.*','partners.name as partner_name');
        $query->leftJoin('partners','invoices.partner_id','=','partners.id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->whereBetween('invoice_date', [$date_start, $date_finish]);
        $invoices = $query->get();
        // dd($healthmeters);
        //Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal Permohonan');
        $sheet->setCellValue('B1', 'Rekanan');
        $sheet->setCellValue('C1', 'Tanggal Penerimaan');
        $sheet->setCellValue('D1', 'Status');
        $sheet->setCellValue('E1', 'Tanggal Akhir');

        $row_number = 2;
        //Content Data
		foreach ($invoices as $invoice) {
            $sheet->setCellValue('A'.$row_number, $invoice->invoice_date);
            $sheet->setCellValue('B'.$row_number, $invoice->partner_name);
            $sheet->setCellValue('C'.$row_number, $invoice->receive_date);
            $sheet->setCellValue('D'.$row_number, $invoice->status);
            $sheet->setCellValue('E'.$row_number, $invoice->close_date);
            $row_number++;
        }
        foreach (range('A', 'E')as $column)
        {
            $sheet->getColumnDimension($column)
            ->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
		$objWriter->save('php://output');
		$export = ob_get_contents();
		ob_end_clean();
		header('Content-Type: application/json');
		if($invoices->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-laporan-invoice-'.date('d-m-Y').'.xlsx',
                'message'	=> "Berhasil Download Data Participant",
                'file' 		=> "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($export)
            ], 200);
		} else {
            return response()->json([
                'status' 	=> false,
                'message'	=> "Data tidak ditemukan",
            ], 400);
		}
    }
}
