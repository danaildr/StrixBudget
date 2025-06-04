<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Counterparty;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показва страницата за импортиране
     */
    public function index()
    {
        return view('import.index');
    }

    /**
     * Генерира шаблон за импортиране
     */
    public function template(Request $request)
    {
        $type = $request->type;
        $format = $request->format;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Задаваме заглавията според типа
        switch ($type) {
            case 'transaction-types':
                $headers = ['name', 'description'];
                break;
            case 'counterparties':
                $headers = ['name', 'email', 'phone', 'description'];
                break;
            case 'bank-accounts':
                $headers = ['name', 'currency', 'initial_balance', 'is_active', 'is_default'];
                break;
            case 'transactions':
                $headers = [
                    'bank_account_name',
                    'counterparty_name',
                    'transaction_type_name',
                    'type',
                    'amount',
                    'description',
                    'executed_at'
                ];
                break;
            case 'transfers':
                $headers = [
                    'from_account_name',
                    'to_account_name',
                    'amount',
                    'description',
                    'executed_at'
                ];
                break;
            default:
                abort(404);
        }

        // Записваме заглавията
        $sheet->fromArray([$headers], null, 'A1');

        // Създаваме writer според формата
        switch ($format) {
            case 'csv':
                $writer = new Writer\Csv($spreadsheet);
                $contentType = 'text/csv';
                break;
            case 'xlsx':
                $writer = new Writer\Xlsx($spreadsheet);
                $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'ods':
                $writer = new Writer\Ods($spreadsheet);
                $contentType = 'application/vnd.oasis.opendocument.spreadsheet';
                break;
            default:
                abort(404);
        }

        // Генерираме файла
        $filename = "template-{$type}.{$format}";
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Импортира типове транзакции
     */
    public function importTransactionTypes(Request $request)
    {
        Log::info('Starting import of transaction types');
        
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,ods']
        ]);

        try {
            Log::info('File validation passed');
            
            DB::beginTransaction();

            $spreadsheet = IOFactory::load($request->file('file'));
            Log::info('File loaded successfully');
            
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            Log::info('Found ' . (count($rows) - 1) . ' rows to import');

            // Премахваме заглавния ред
            $headers = array_shift($rows);

            /** @var User $user */
            $user = Auth::user();

            foreach ($rows as $row) {
                if (empty($row[0])) continue; // Пропускаме празни редове

                Log::info('Importing row: ' . json_encode($row));

                $user->transactionTypes()->create([
                    'name' => $row[0],
                    'description' => $row[1] ?? null,
                ]);
            }

            DB::commit();
            Log::info('Import completed successfully');
            
            return back()->with('success', 'Transaction types imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Error importing transaction types: ' . $e->getMessage());
        }
    }

    /**
     * Импортира контрагенти
     */
    public function importCounterparties(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,ods']
        ]);

        try {
            DB::beginTransaction();

            $spreadsheet = IOFactory::load($request->file('file'));
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Премахваме заглавния ред
            $headers = array_shift($rows);

            /** @var User $user */
            $user = Auth::user();

            foreach ($rows as $row) {
                if (empty($row[0])) continue; // Пропускаме празни редове

                $user->counterparties()->create([
                    'name' => $row[0],
                    'email' => $row[1] ?? null,
                    'phone' => $row[2] ?? null,
                    'description' => $row[3] ?? null,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Counterparties imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing counterparties: ' . $e->getMessage());
        }
    }

    /**
     * Импортира банкови сметки
     */
    public function importBankAccounts(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,ods']
        ]);

        try {
            DB::beginTransaction();

            $spreadsheet = IOFactory::load($request->file('file'));
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Премахваме заглавния ред
            $headers = array_shift($rows);

            /** @var User $user */
            $user = Auth::user();

            foreach ($rows as $row) {
                if (empty($row[0])) continue; // Пропускаме празни редове

                // Ако сметката е маркирана като default, премахваме default от другите сметки
                if (!empty($row[4]) && filter_var($row[4], FILTER_VALIDATE_BOOLEAN)) {
                    $user->bankAccounts()->where('is_default', true)->update(['is_default' => false]);
                }

                $user->bankAccounts()->create([
                    'name' => $row[0],
                    'currency' => $row[1],
                    'balance' => $row[2] ?? 0,
                    'is_active' => isset($row[3]) ? filter_var($row[3], FILTER_VALIDATE_BOOLEAN) : true,
                    'is_default' => isset($row[4]) ? filter_var($row[4], FILTER_VALIDATE_BOOLEAN) : false,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Bank accounts imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing bank accounts: ' . $e->getMessage());
        }
    }

    /**
     * Импортира транзакции
     */
    public function importTransactions(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,ods']
        ]);

        try {
            DB::beginTransaction();

            $spreadsheet = IOFactory::load($request->file('file'));
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Премахваме заглавния ред
            $headers = array_shift($rows);

            /** @var User $user */
            $user = Auth::user();

            foreach ($rows as $row) {
                if (empty($row[0])) continue; // Пропускаме празни редове

                // Намираме банковата сметка
                $bankAccount = $user->bankAccounts()
                    ->where('name', $row[0])
                    ->firstOrFail();

                // Намираме контрагента
                $counterparty = $user->counterparties()
                    ->where('name', $row[1])
                    ->firstOrFail();

                // Намираме типа транзакция
                $transactionType = $user->transactionTypes()
                    ->where('name', $row[2])
                    ->firstOrFail();

                $transaction = $user->transactions()->create([
                    'bank_account_id' => $bankAccount->id,
                    'counterparty_id' => $counterparty->id,
                    'transaction_type_id' => $transactionType->id,
                    'type' => $row[3],
                    'amount' => $row[4],
                    'description' => $row[5] ?? null,
                    'executed_at' => $row[6],
                ]);

                // Актуализираме баланса на сметката
                $transaction->updateAccountBalance();
            }

            DB::commit();
            return back()->with('success', 'Transactions imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing transactions: ' . $e->getMessage());
        }
    }

    /**
     * Импортира трансфери
     */
    public function importTransfers(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,ods']
        ]);

        try {
            DB::beginTransaction();

            $spreadsheet = IOFactory::load($request->file('file'));
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Премахваме заглавния ред
            $headers = array_shift($rows);

            /** @var User $user */
            $user = Auth::user();

            foreach ($rows as $row) {
                if (empty($row[0])) continue; // Пропускаме празни редове

                // Намираме изходящата сметка
                $fromAccount = $user->bankAccounts()
                    ->where('name', $row[0])
                    ->firstOrFail();

                // Намираме входящата сметка
                $toAccount = $user->bankAccounts()
                    ->where('name', $row[1])
                    ->firstOrFail();

                $transfer = $user->transfers()->create([
                    'from_account_id' => $fromAccount->id,
                    'to_account_id' => $toAccount->id,
                    'amount' => $row[2],
                    'description' => $row[3] ?? null,
                    'executed_at' => $row[4],
                ]);

                // Актуализираме балансите на сметките
                $fromAccount->withdraw($transfer->amount);
                $toAccount->deposit($transfer->amount);
            }

            DB::commit();
            return back()->with('success', 'Transfers imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing transfers: ' . $e->getMessage());
        }
    }
}
