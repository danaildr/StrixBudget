<div class="mb-4">
    <label for="bank_account_id" class="block font-semibold mb-1">{{ __('Bank Account') }}</label>
    <select name="bank_account_id" id="bank_account_id" class="form-select w-full" required>
        <option value="">{{ __('Choose...') }}</option>
        @foreach($accounts as $account)
            <option value="{{ $account->id }}" @selected(old('bank_account_id', isset($payment) ? $payment->bank_account_id : null) == $account->id)>{{ $account->name }}{{ $account->iban ? ' - ' . $account->iban : '' }}</option>
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
    <label for="scheduled_date" class="block font-semibold mb-1">{{ __('Scheduled Date') }}</label>
    <input type="date" name="scheduled_date" id="scheduled_date" class="form-input w-full" value="{{ old('scheduled_date', isset($payment) ? $payment->scheduled_date : null) }}" required>
    @error('scheduled_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
</div>
<div class="mb-4 flex gap-2">
    <div class="w-1/2">
        <label for="period_start_date" class="block font-semibold mb-1">{{ __('Period from') }}</label>
        <input type="date" name="period_start_date" id="period_start_date" class="form-input w-full" value="{{ old('period_start_date', isset($payment) ? $payment->period_start_date : null) }}">
        @error('period_start_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="w-1/2">
        <label for="period_end_date" class="block font-semibold mb-1">{{ __('Period to') }}</label>
        <input type="date" name="period_end_date" id="period_end_date" class="form-input w-full" value="{{ old('period_end_date', isset($payment) ? $payment->period_end_date : null) }}">
        @error('period_end_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-4">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked(old('is_active', isset($payment) ? $payment->is_active : true))>
        <span class="ml-2">{{ __('Active') }}</span>
    </label>
</div>
<!-- Removed submit button for form partial --> 