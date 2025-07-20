<div class="mb-4">
    <label for="bank_account_id" class="block font-semibold mb-1">{{ __('Bank Account') }}</label>
    <select name="bank_account_id" id="bank_account_id" class="form-select w-full" required>
        <option value="">{{ __('Choose...') }}</option>
        @foreach($accounts as $account)
            <option value="{{ $account->id }}" @selected(old('bank_account_id', isset($payment) ? $payment->bank_account_id : null) == $account->id)>{{ $account->name }}</option>
        @endforeach
    </select>
    @error('bank_account_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="counterparty_id" class="block font-semibold mb-1">{{ __('Counterparty') }}</label>
    <select name="counterparty_id" id="counterparty_id" class="form-select w-full">
        <option value="">{{ __('No counterparty') }}</option>
        @foreach($counterparties as $counterparty)
            <option value="{{ $counterparty->id }}" @selected(old('counterparty_id', isset($payment) ? $payment->counterparty_id : null) == $counterparty->id)>{{ $counterparty->name }}</option>
        @endforeach
    </select>
    @error('counterparty_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="transaction_type_id" class="block font-semibold mb-1">{{ __('Category') }}</label>
    <select name="transaction_type_id" id="transaction_type_id" class="form-select w-full">
        <option value="">{{ __('No category') }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('transaction_type_id', isset($payment) ? $payment->transaction_type_id : null) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    @error('transaction_type_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="amount" class="block font-semibold mb-1">{{ __('Amount') }}</label>
    <input type="number" step="0.01" name="amount" id="amount" class="form-input w-full" value="{{ old('amount', isset($payment) ? $payment->amount : null) }}" required>
    @error('amount')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="currency" class="block font-semibold mb-1">{{ __('Currency') }}</label>
    <input type="text" name="currency" id="currency" class="form-input w-full" value="{{ old('currency', isset($payment) ? $payment->currency : 'BGN') }}" maxlength="3" required>
    @error('currency')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="description" class="block font-semibold mb-1">{{ __('Description') }}</label>
    <input type="text" name="description" id="description" class="form-input w-full" value="{{ old('description', isset($payment) ? $payment->description : null) }}">
    @error('description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4">
    <label for="repeat_type" class="block font-semibold mb-1">{{ __('Repeat Type') }}</label>
    <select name="repeat_type" id="repeat_type" class="form-select w-full" required onchange="toggleCustomRepeat()">
        <option value="daily" @selected(old('repeat_type', isset($payment) ? $payment->repeat_type : null)=='daily')>{{ __('Daily') }}</option>
        <option value="weekly" @selected(old('repeat_type', isset($payment) ? $payment->repeat_type : null)=='weekly')>{{ __('Weekly') }}</option>
        <option value="monthly" @selected(old('repeat_type', isset($payment) ? $payment->repeat_type : null)=='monthly')>{{ __('Monthly') }}</option>
        <option value="yearly" @selected(old('repeat_type', isset($payment) ? $payment->repeat_type : null)=='yearly')>{{ __('Yearly') }}</option>
        <option value="custom" @selected(old('repeat_type', isset($payment) ? $payment->repeat_type : null)=='custom')>{{ __('Custom') }}</option>
    </select>
    @error('repeat_type')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4" id="custom-repeat-fields" style="display: none;">
    <label class="block font-semibold mb-1">{{ __('Custom') }} {{ __('Period') }}</label>
    <div class="flex gap-2">
        <input type="number" name="repeat_interval" id="repeat_interval" class="form-input w-1/2" min="1" value="{{ old('repeat_interval', isset($payment) ? $payment->repeat_interval : null) }}" placeholder="{{ __('Interval') }}">
        <select name="repeat_unit" id="repeat_unit" class="form-select w-1/2">
            <option value="days" @selected(old('repeat_unit', isset($payment) ? $payment->repeat_unit : null)=='days')>{{ __('Daily') }}</option>
            <option value="months" @selected(old('repeat_unit', isset($payment) ? $payment->repeat_unit : null)=='months')>{{ __('Monthly') }}</option>
            <option value="years" @selected(old('repeat_unit', isset($payment) ? $payment->repeat_unit : null)=='years')>{{ __('Yearly') }}</option>
        </select>
    </div>
    @error('repeat_interval')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    @error('repeat_unit')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4 flex gap-2">
    <div class="w-1/2">
        <label for="period_start_day" class="block font-semibold mb-1">{{ __('Day from (month)') }}</label>
        <input type="number" name="period_start_day" id="period_start_day" class="form-input w-full" min="1" max="31" value="{{ old('period_start_day', isset($payment) ? $payment->period_start_day : null) }}">
        @error('period_start_day')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="w-1/2">
        <label for="period_end_day" class="block font-semibold mb-1">{{ __('Day to (month)') }}</label>
        <input type="number" name="period_end_day" id="period_end_day" class="form-input w-full" min="1" max="31" value="{{ old('period_end_day', isset($payment) ? $payment->period_end_day : null) }}">
        @error('period_end_day')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-4 flex gap-2">
    <div class="w-1/2">
        <label for="start_date" class="block font-semibold mb-1">{{ __('Start Date') }}</label>
        <input type="date" name="start_date" id="start_date" class="form-input w-full" value="{{ old('start_date', isset($payment) ? $payment->start_date : null) }}" required>
        @error('start_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="w-1/2">
        <label for="end_date" class="block font-semibold mb-1">{{ __('End Date') }}</label>
        <input type="date" name="end_date" id="end_date" class="form-input w-full" value="{{ old('end_date', isset($payment) ? $payment->end_date : null) }}">
        @error('end_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-4">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked(old('is_active', isset($payment) ? $payment->is_active : true))>
        <span class="ml-2">{{ __('Active') }}</span>
    </label>
</div>
<!-- Removed submit button for form partial --> 