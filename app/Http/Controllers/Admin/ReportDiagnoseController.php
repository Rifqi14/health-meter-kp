<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CheckupResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class ReportDiagnoseController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/reportdiagnose'));
    }

    public function index()
    {
        return view('admin.reportdiagnose.index');
    }

    public function readDiagnose(Request $request)
    {
        $start              = $request->start;
        $length             = $request->length;
        $query              = $request->search['value'];
        $sort               = $request->columns[$request->order[0]['column']]['data'];
        $dir                = $request->order[0]['dir'];
        $site_id            = $request->site_id ? explode(',', $request->site_id) : null;
        $examination_type_id= $request->examination_type_id ? explode(',', $request->examination_type_id) : null;
        $date               = explode(' - ', $request->date);
        $from               = Carbon::parse($date[0])->toDateString();
        $to                 = Carbon::parse($date[1])->toDateString();

        // Count Data
        $query              = CheckupResult::with(['patient', 'patientsite', 'workforce', 'examinationtype'])->whereBetween('date', [$from, $to]);
        if ($site_id) {
            $query->whereIn('patient_site_id', $site_id);
        }
        if ($examination_type_id) {
            $query->whereIn('examination_type_id', $examination_type_id);
        }
        $recordsTotal       = $query->count();

        // Select Pagination
        $query              = CheckupResult::with(['patient', 'patientsite', 'workforce', 'examinationtype'])->whereBetween('date', [$from, $to]);
        if ($site_id) {
            $query->whereIn('patient_site_id', $site_id);
        }
        if ($examination_type_id) {
            $query->whereIn('examination_type_id', $examination_type_id);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $reportDiagnoses = $query->get();

        $data = [];
        foreach ($reportDiagnoses as $key => $value) {
            $value->no      = ++$start;
            $data[]         = $value;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data,
        ], 200);
    }

    public function exportDiagnose(Request $request)
    {
        $site_id                = $request->site_id ? explode(',', $request->site_id) : null;
        $examination_type_id    = $request->examination_type_id ? explode(',', $request->examination_type_id) : null;
        $date                   = explode(' - ', $request->date);
        $from                   = Carbon::parse($date[0])->toDateString();
        $to                     = Carbon::parse($date[1])->toDateString();

        $query              = CheckupResult::with(['patient', 'patientsite', 'workforce', 'examinationtype'])->whereBetween('date', [$from, $to]);
        if ($site_id) {
            $query->whereIn('patient_site_id', $site_id);
        }
        if ($examination_type_id) {
            $query->whereIn('examination_type_id', $examination_type_id);
        }
        $query->orderBy('date', 'asc');
        $exportDiagnose = $query->get();

        $object         = new \PHPExcel();
        $object->getProperties()->setCreator('Health Meter KP');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        // Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'NID Workforce');
        $sheet->setCellValue('C1', 'Nama Workforce');
        $sheet->setCellValue('D1', 'Nama Pasien');
        $sheet->setCellValue('E1', 'Status Pasien');
        $sheet->setCellValue('F1', 'Distrik Pasien');
        $sheet->setCellValue('G1', 'Jenis Pemeriksaan');
        $sheet->setCellValue('H1', 'Hasil Pemeriksaan');
        $sheet->setCellValue('I1', 'Batas Normal');

        $row_number = 2;

        // Content Data
        foreach ($exportDiagnose as $key => $value) {
            $sheet->setCellValue('A'.$row_number, @$value->date);
            $sheet->setCellValue('B'.$row_number, @$value->workforce->nid);
            $sheet->setCellValue('C'.$row_number, @$value->workforce->name);
            $sheet->setCellValue('D'.$row_number, @$value->patient->name);
            $sheet->setCellValue('E'.$row_number, @$value->patient->status);
            $sheet->setCellValue('F'.$row_number, @$value->patientsite->name);
            $sheet->setCellValue('G'.$row_number, @$value->examinationtype->name);
            $sheet->setCellValue('H'.$row_number, @$value->result);
            $sheet->setCellValue('I'.$row_number, @$value->normal_limit);
            $row_number++;
        }

        foreach (range('A', 'I') as $key => $value) {
            $sheet->getColumnDimension($value)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
		ob_end_clean();
		header('Content-Type: application/json');
		if($exportDiagnose->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-laporan-surat-diagnosa-'.date('d-m-Y').'.xlsx',
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
}