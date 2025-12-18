<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetDate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date_birth_init' => 'required|date',
            'date_birth_end' => 'required|date|after_or_equal:date_birth_init',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->has('user_id') && $request->user_id) {
                $users = User::where('id', $request->user_id)->get();
            } else {
                $users = User::whereNotNull('date_birth_init')
                    ->whereNotNull('date_birth_end')
                    ->where('date_birth_init', '<=', $request->date_birth_end)
                    ->where('date_birth_end', '>=', $request->date_birth_init)
                    ->orderBy('name')
                    ->get();
            }

            if ($request->has('user_id') && $request->user_id && $users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Reporte de Usuarios');

            $headerRow = 1;
            $sheet->setCellValue('A' . $headerRow, 'ID');
            $sheet->setCellValue('B' . $headerRow, 'Título');
            $sheet->setCellValue('C' . $headerRow, 'Fecha de Creación');
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A' . $headerRow . ':C' . $headerRow)->applyFromArray($headerStyle);
            $sheet->getRowDimension($headerRow)->setRowHeight(25);
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];

            $report = Report::create([
                'title' => $request->title,
                'report_link' => null,
            ]);

            $row = $headerRow + 1;
            $fillColor = 'FFFFFF';

            $sheet->setCellValue('A' . $row, $report->id);
            $sheet->setCellValue('B' . $row, $report->title);

            try {
                $excelDateTime = PhpSpreadsheetDate::dateTimeToExcel($report->created_at);
                $sheet->setCellValue('C' . $row, $excelDateTime);
                $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
            } catch (\Exception $e) {
                $sheet->setCellValue('C' . $row, $report->created_at->format('Y-m-d H:i:s'));
            }

            $cellStyle = array_merge($dataStyle, [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor],
                ],
            ]);
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($cellStyle);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('C')->setWidth(25);

            $fileName = 'report_' . time() . '_' . uniqid() . '.xlsx';
            $filePath = storage_path('app/temp/' . $fileName);

            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            $report->update([
                'report_link' => $fileName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reporte generado exitosamente',
                'data' => [
                    'id' => $report->id,
                    'title' => $report->title,
                    'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                    'users_count' => $users->count(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function get($id)
    {
        try {
            $report = Report::findOrFail($id);

            if (!$report->report_link) {
                return response()->json([
                    'success' => false,
                    'message' => 'El reporte no tiene archivo asociado',
                ], 404);
            }

            $filePath = storage_path('app/temp/' . $report->report_link);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo del reporte no existe',
                ], 404);
            }

            return response()->download($filePath, $report->title . '.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar el reporte',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function list()
    {
        try {
            $reports = Report::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $reports->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'title' => $report->title,
                        'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                        'report_link' => $report->report_link,
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar los reportes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
