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
                $headers = ['Name', 'Description'];
                break;
            case 'counterparties':
                $headers = ['Name', 'Email', 'Phone', 'Description'];
                break;
            case 'bank-accounts':
                $headers = ['Name', 'Currency (BGN/EUR/USD)', 'Initial Balance', 'Is Active (true/false)', 'Is Default (true/false)'];
                break;
            case 'transactions':
                $headers = [
                    'Bank Account',
                    'Counterparty',
                    'Transaction Type',
                    'Type (income/expense)',
                    'Amount',
                    'Description',
                    'Date (YYYY-MM-DD)'
                ];
                break;
            case 'transfers':
                $headers = [
                    'From Account',
                    'To Account',
                    'Amount',
                    'Description',
                    'Date (YYYY-MM-DD)',
                    'Exchange Rate (optional)'
                ];
                break;
            default:
                abort(404);
        }

        // Записваме заглавията
        $sheet->fromArray([$headers], null, 'A1');

        // Добавяме примерни данни за по-добро разбиране
        switch ($type) {
            case 'transactions':
                $exampleData = [
                    'Основна сметка',
                    'Електроразпределение',
                    'Комунални услуги',
                    'expense',
                    '45.50',
                    'Ток за декември',
                    '2025-01-15'
                ];
                $sheet->fromArray([$exampleData], null, 'A2');
                break;
            case 'transfers':
                $exampleData = [
                    'Основна сметка',
                    'Спестовна сметка',
                    '500.00',
                    'Месечно спестяване',
                    '2025-01-15',
                    '' // Exchange rate е опционален
                ];
                $sheet->fromArray([$exampleData], null, 'A2');
                break;
            case 'transaction-types':
                $exampleData = ['Комунални услуги', 'Разходи за ток, вода, газ'];
                $sheet->fromArray([$exampleData], null, 'A2');
                break;
            case 'counterparties':
                $exampleData = ['Електроразпределение', 'office@electricity.bg', '+359888123456', 'Доставчик на електричество'];
                $sheet->fromArray([$exampleData], null, 'A2');
                break;
            case 'bank-accounts':
                $exampleData = ['Основна сметка', 'BGN', '1000.00', 'true', 'true'];
                $sheet->fromArray([$exampleData], null, 'A2');
                break;
        }

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
     *
     * Очакван формат на CSV файла:
     * Колона 0: Име на банкова сметка
     * Колона 1: Име на контрагент
     * Колона 2: Име на тип транзакция
     * Колона 3: Тип (income/expense)
     * Колона 4: Сума
     * Колона 5: Описание (опционално)
     * Колона 6: Дата на изпълнение
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

                // Обработваме датата
                $executedAt = $row[6];
                if (!empty($executedAt)) {
                    try {
                        $executedAt = \Carbon\Carbon::parse($executedAt);
                    } catch (\Exception $e) {
                        $executedAt = now(); // Fallback към текущата дата
                    }
                } else {
                    $executedAt = now();
                }

                $transaction = $user->transactions()->create([
                    'bank_account_id' => $bankAccount->id,
                    'counterparty_id' => $counterparty->id,
                    'transaction_type_id' => $transactionType->id,
                    'type' => $row[3],
                    'amount' => $row[4],
                    'currency' => $bankAccount->currency, // Използваме валутата на сметката
                    'description' => $row[5] ?? null,
                    'executed_at' => $executedAt,
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
     *
     * Очакван формат на CSV файла:
     * Колона 0: Име на изходяща банкова сметка
     * Колона 1: Име на входяща банкова сметка
     * Колона 2: Сума (в валутата на изходящата сметка)
     * Колона 3: Описание (опционално)
     * Колона 4: Дата на изпълнение
     * Колона 5: Exchange rate (опционално, само при различни валути)
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

                // Изчисляваме exchange rate и amount_to
                $amountFrom = $row[2];
                $currencyFrom = $fromAccount->currency;
                $currencyTo = $toAccount->currency;
                $description = $row[3] ?? null;

                // Обработваме датата
                $executedAt = $row[4];
                if (!empty($executedAt)) {
                    try {
                        $executedAt = \Carbon\Carbon::parse($executedAt);
                    } catch (\Exception $e) {
                        $executedAt = now(); // Fallback към текущата дата
                    }
                } else {
                    $executedAt = now();
                }

                // Ако валутите са еднакви, exchange rate е 1
                $exchangeRate = 1.0;
                $amountTo = $amountFrom;

                // Ако има различни валути и е предоставен exchange rate в колона 5
                if ($currencyFrom !== $currencyTo && isset($row[5]) && !empty($row[5])) {
                    $exchangeRate = (float) $row[5];
                    $amountTo = $amountFrom * $exchangeRate;
                }

                $transfer = $user->transfers()->create([
                    'from_account_id' => $fromAccount->id,
                    'to_account_id' => $toAccount->id,
                    'amount_from' => $amountFrom,
                    'currency_from' => $currencyFrom,
                    'amount_to' => $amountTo,
                    'currency_to' => $currencyTo,
                    'exchange_rate' => $exchangeRate,
                    'description' => $description,
                    'executed_at' => $executedAt,
                ]);

                // Актуализираме балансите на сметките
                $fromAccount->withdraw($transfer->amount_from);
                $toAccount->deposit($transfer->amount_to);
            }

            DB::commit();
            return back()->with('success', 'Transfers imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing transfers: ' . $e->getMessage());
        }
    }
}
