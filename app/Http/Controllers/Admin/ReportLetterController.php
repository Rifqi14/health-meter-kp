<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CoveringLetter;
use App\Models\HealthInsurance;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class ReportLetterController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/reportletter'));
    }

    public function readCoverLetter(Request $request)
    {
        $start           = $request->start;
        $length          = $request->length;
        $query           = $request->search['value'];
        $sort            = $request->columns[$request->order[0]['column']]['data'];
        $dir             = $request->order[0]['dir'];
        $site_id            = $request->site_id;
        $cover_letter_type  = strtoupper($request->cover_letter_type);
        $date               = explode(' - ', $request->date);
        $from               = Carbon::parse($date[0])->toDateString();
        $to                 = Carbon::parse($date[1])->toDateString();

        // Count Data
        $query              = CoveringLetter::with(['updatedby.workforce.site', 'workforce', 'patient', 'patientsite'])->whereBetween('letter_date', [$from, $to]);
        if ($site_id) {
            $query->whereHas('updatedby.workforce.site', function($q) use($site_id) {
                $q->where('id', $site_id);
            });
        }
        if ($cover_letter_type) {
            $query->whereRaw("upper(type) like '%$cover_letter_type%'");
        }
        $recordsTotal       = $query->count();

        // Select Pagination
        $query              = CoveringLetter::with(['updatedby.workforce.site', 'workforce', 'patient', 'patientsite'])->whereBetween('letter_date', [$from, $to]);
        if ($site_id) {
            $query->whereHas('updatedby.workforce.site', function($q) use($site_id) {
                $q->where('id', $site_id);
            });
        }
        if ($cover_letter_type) {
            $query->whereRaw("upper(type) like '%$cover_letter_type%'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $reportCoverLetters = $query->get();

        $data   = [];
        foreach ($reportCoverLetters as $key => $value) {
            $value->no  = ++$start;
            $data[]     = $value;
        }
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function readHealthInsurance(Request $request)
    {
        $start           = $request->start;
        $length          = $request->length;
        $query           = $request->search['value'];
        $sort            = $request->columns[$request->order[0]['column']]['data'];
        $dir             = $request->order[0]['dir'];
        $site_id            = $request->site_id;
        $cover_letter_type  = strtoupper($request->cover_letter_type);
        $date               = explode(' - ', $request->date);
        $from               = Carbon::parse($date[0])->toDateString();
        $to                 = Carbon::parse($date[1])->toDateString();

        // Count Data
        $query              = HealthInsurance::with(['lettermaker', 'lettermakersite', 'workforce', 'patient', 'patientsite'])->whereBetween('date', [$from, $to]);
        if ($site_id) {
            $query->where('letter_maker_site_id', $site_id);
        }
        if ($cover_letter_type) {
            $query->whereRaw("upper(cover_letter_type) like '%$cover_letter_type%'");
        }
        $recordsTotal       = $query->count();

        // Select Pagination
        $query              = HealthInsurance::with(['lettermaker', 'lettermakersite', 'workforce', 'patient', 'patientsite'])->whereBetween('date', [$from, $to]);
        if ($site_id) {
            $query->where('letter_maker_site_id', $site_id);
        }
        if ($cover_letter_type) {
            $query->whereRaw("upper(cover_letter_type) like '%$cover_letter_type%'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $reportHealthInsurances = $query->get();

        $data   = [];
        foreach ($reportHealthInsurances as $key => $value) {
            $value->no  = ++$start;
            $data[]     = $value;
        }
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function exportLetter(Request $request)
    {
        $site_id            = $request->site_id;
        $cover_letter_type  = strtoupper($request->cover_letter_type);
        $date               = explode(' - ', $request->date);
        $from               = Carbon::parse($date[0])->toDateString();
        $to                 = Carbon::parse($date[1])->toDateString();

        $query              = CoveringLetter::with(['updatedby.workforce.site', 'workforce', 'patient', 'patientsite'])->whereBetween('letter_date', [$from, $to]);
        if ($site_id) {
            $query->whereHas('updatedby.workforce.site', function($q) use($site_id) {
                $q->where('id', $site_id);
            });
        }
        $query->whereRaw("upper(type) like '%$cover_letter_type%'");
        $query->orderBy('letter_date', 'desc');
        $exportCoverLetters = $query->get();

        $object         = new \PHPExcel();
        $object->getProperties()->setCreator('Health Meter KP');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        // Header Column Excel
        $sheet->setCellValue('A1', 'Tipe Surat');
        $sheet->setCellValue('B1', 'Kode Distrik Pembuat');
        $sheet->setCellValue('C1', 'Tanggal Surat');
        $sheet->setCellValue('D1', 'Nama Workforce');
        $sheet->setCellValue('E1', 'NID Workforce');
        $sheet->setCellValue('F1', 'Nama Pasien');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'Distrik Pasien');
        $sheet->setCellValue('I1', 'Status Proses Surat');
        $sheet->setCellValue('J1', 'Link Dokumen');

        $row_number = 2;
        // Content Data
        foreach ($exportCoverLetters as $key => $value) {
            $sheet->setCellValue('A'.$row_number, @$value->type);
            $sheet->setCellValue('B'.$row_number, $value->updated_by ? $value->updatedby->workforce->site->name : '');
            $sheet->setCellValue('C'.$row_number, @$value->letter_date);
            $sheet->setCellValue('D'.$row_number, @$value->workforce->name);
            $sheet->setCellValue('E'.$row_number, @$value->workforce->nid);
            $sheet->setCellValue('F'.$row_number, @$value->patient->name);
            $sheet->setCellValue('G'.$row_number, @$value->patient->status);
            $sheet->setCellValue('H'.$row_number, @$value->patient->site->name);
            $sheet->setCellValue('I'.$row_number, @$value->status);
            $sheet->setCellValue('J'.$row_number, @$value->document_link);
            $row_number++;
        }

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
		ob_end_clean();
		header('Content-Type: application/json');
		if($exportCoverLetters->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-laporan-surat-'.@$cover_letter_type.'-'.date('d-m-Y').'.xlsx',
                'message'	=> "Berhasil Download Data Surat",
                'file' 		=> "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($export)
            ], 200);
		} else {
            return response()->json([
                'status' 	=> false,
                'message'	=> "Data tidak ditemukan",
            ], 400);
		}
    }

    public function exportInsurance(Request $request)
    {
        $site_id            = $request->site_id;
        $cover_letter_type  = strtoupper($request->cover_letter_type);
        $date               = explode(' - ', $request->date);
        $from               = Carbon::parse($date[0])->toDateString();
        $to                 = Carbon::parse($date[1])->toDateString();

        $query              = HealthInsurance::with(['lettermaker', 'lettermakersite', 'workforce', 'patient', 'patientsite'])->whereBetween('date', [$from, $to]);
        if ($site_id) {
            $query->where('letter_maker_site_id', $site_id);
        }
        $query->whereRaw("upper(cover_letter_type) like '%$cover_letter_type%'");
        $query->orderBy('date', 'desc');
        $exportCoverLetters = $query->get();

        $object         = new \PHPExcel();
        $object->getProperties()->setCreator('Health Meter KP');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        // Header Column Excel
        $sheet->setCellValue('A1', 'Tipe Surat');
        $sheet->setCellValue('B1', 'Kode Distrik Pembuat');
        $sheet->setCellValue('C1', 'Tanggal Surat');
        $sheet->setCellValue('D1', 'Nama Workforce');
        $sheet->setCellValue('E1', 'NID Workforce');
        $sheet->setCellValue('F1', 'Nama Pasien');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'Distrik Pasien');
        $sheet->setCellValue('I1', 'Status Proses Surat');
        $sheet->setCellValue('J1', 'Link Dokumen');

        $row_number = 2;
        // Content Data
        foreach ($exportCoverLetters as $key => $value) {
            $sheet->setCellValue('A'.$row_number, @$value->cover_letter_type);
            $sheet->setCellValue('B'.$row_number, $value->letter_maker_id ? $value->lettermaker->name : '');
            $sheet->setCellValue('C'.$row_number, @$value->date);
            $sheet->setCellValue('D'.$row_number, @$value->workforce->name);
            $sheet->setCellValue('E'.$row_number, @$value->workforce->nid);
            $sheet->setCellValue('F'.$row_number, @$value->patient->name);
            $sheet->setCellValue('G'.$row_number, @$value->patient->status);
            $sheet->setCellValue('H'.$row_number, @$value->patientsite->name);
            switch ($value->status) {
                case 0:
                    $sheet->setCellValue('I'.$row_number, 'Draft');
                    break;
                case 1:
                    $sheet->setCellValue('I'.$row_number, 'Printed');
                    break;
                
                default:
                    $sheet->setCellValue('I'.$row_number, 'Uploaded');
                    break;
            }
            $sheet->setCellValue('J'.$row_number, @$value->document_link);
            $row_number++;
        }

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
		ob_end_clean();
		header('Content-Type: application/json');
		if($exportCoverLetters->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-laporan-surat-'.@$cover_letter_type.'-'.date('d-m-Y').'.xlsx',
                'message'	=> "Berhasil Download Data Surat",
                'file' 		=> "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($export)
            ], 200);
		} else {
            return response()->json([
                'status' 	=> false,
                'message'	=> "Data tidak ditemukan",
            ], 400);
		}
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reportletter.index');
    }

    public function chartCoverLetter(Request $request)
    {
        $site_id            = $request->site_id;
        $cover_letter_type  = strtoupper($request->cover_letter_type);
        $date               = Carbon::parse($request->date)->toDateString();
    }
}