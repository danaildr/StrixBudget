<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportTransactions(Request $request, string $format)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $query = $user->transactions()
            ->with(['bankAccount', 'counterparty', 'transactionType']);

        // Прилагане на филтрите
        if ($request->filled('start_date')) {
            $query->whereDate('executed_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('executed_at', '<=', $request->end_date);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }
        if ($request->filled('counterparty_id')) {
            $query->where('counterparty_id', $request->counterparty_id);
        }
        if ($request->filled('transaction_type_id')) {
            $query->where('transaction_type_id', $request->transaction_type_id);
        }

        $transactions = $query->latest('executed_at')->get();

        return match($format) {
            'csv' => $this->exportToCsv($transactions, 'transactions'),
            'xlsx' => $this->exportToXlsx($transactions, 'transactions'),
            'ods' => $this->exportToOds($transactions, 'transactions'),
            'pdf' => $this->exportToPdf($transactions, 'transactions'),
            'json' => $this->exportToJson($transactions, 'transactions'),
            'html' => $this->exportToHtml($transactions, 'transactions'),
            default => abort(400, 'Unsupported format'),
        };
    }

    public function exportTransfers(Request $request, string $format)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $query = $user->transfers()
            ->with(['fromAccount', 'toAccount']);

        // Прилагане на филтрите
        if ($request->filled('start_date')) {
            $query->whereDate('executed_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('executed_at', '<=', $request->end_date);
        }
        if ($request->filled('from_account_id')) {
            $query->where('from_account_id', $request->from_account_id);
        }
        if ($request->filled('to_account_id')) {
            $query->where('to_account_id', $request->to_account_id);
        }
        if ($request->filled('min_amount')) {
            $query->where('amount_from', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount_from', '<=', $request->max_amount);
        }

        $transfers = $query->latest('executed_at')->get();

        return match($format) {
            'csv' => $this->exportToCsv($transfers, 'transfers'),
            'xlsx' => $this->exportToXlsx($transfers, 'transfers'),
            'ods' => $this->exportToOds($transfers, 'transfers'),
            'pdf' => $this->exportToPdf($transfers, 'transfers'),
            'json' => $this->exportToJson($transfers, 'json'),
            'html' => $this->exportToHtml($transfers, 'transfers'),
            default => abort(400, 'Unsupported format'),
        };
    }

    protected function exportToCsv($data, string $type)
    {
        $spreadsheet = $this->createSpreadsheet($data, $type);
        $writer = new Csv($spreadsheet);
        
        $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    protected function exportToXlsx($data, string $type)
    {
        $spreadsheet = $this->createSpreadsheet($data, $type);
        $writer = new Xlsx($spreadsheet);
        
        $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    protected function exportToOds($data, string $type)
    {
        $spreadsheet = $this->createSpreadsheet($data, $type);
        $writer = new Ods($spreadsheet);
        
        $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '.ods';
        header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    protected function exportToPdf($data, string $type)
    {
        $view = 'exports.' . $type . '_pdf';
        $pdf = PDF::loadView($view, [$type => $data]);
        
        return $pdf->download($type . '_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    protected function exportToJson($data, string $type)
    {
        $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '.json';
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    protected function exportToHtml($data, string $type)
    {
        $view = 'exports.' . $type . '_html';
        $content = view($view, [$type => $data])->render();
        
        $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '.html';
        
        return response($content)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    protected function createSpreadsheet($data, string $type): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        if ($type === 'transactions') {
            $headers = ['Date', 'Type', 'Category', 'Account', 'Counterparty', 'Amount', 'Currency', 'Description'];
            $sheet->fromArray($headers, null, 'A1');
            
            $row = 2;
            foreach ($data as $transaction) {
                $sheet->fromArray([
                    $transaction->executed_at->format('Y-m-d H:i'),
                    ucfirst($transaction->type),
                    $transaction->transactionType->name,
                    $transaction->bankAccount->name,
                    $transaction->counterparty->name,
                    $transaction->amount,
                    $transaction->currency,
                    $transaction->description,
                ], null, 'A' . $row);
                $row++;
            }
        } else {
            $headers = ['Date', 'From Account', 'To Account', 'Amount From', 'Currency From', 'Amount To', 'Currency To', 'Exchange Rate', 'Description'];
            $sheet->fromArray($headers, null, 'A1');
            
            $row = 2;
            foreach ($data as $transfer) {
                $sheet->fromArray([
                    $transfer->executed_at->format('Y-m-d H:i'),
                    $transfer->fromAccount->name,
                    $transfer->toAccount->name,
                    $transfer->amount_from,
                    $transfer->currency_from,
                    $transfer->amount_to,
                    $transfer->currency_to,
                    $transfer->exchange_rate,
                    $transfer->description,
                ], null, 'A' . $row);
                $row++;
            }
        }

        return $spreadsheet;
    }
} 