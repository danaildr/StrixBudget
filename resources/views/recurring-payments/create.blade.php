<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Recurring Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('recurring-payments.store') }}" class="space-y-6">
                        @csrf
                        @include('recurring-payments._form-fields')
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create') }}</x-primary-button>
                            <a href="{{ route('recurring-payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    function toggleCustomRepeat() {
        const repeatType = document.getElementById('repeat_type').value;
        document.getElementById('custom-repeat-fields').style.display = repeatType === 'custom' ? '' : 'none';
    }
    document.addEventListener('DOMContentLoaded', toggleCustomRepeat);
    document.getElementById('repeat_type').addEventListener('change', toggleCustomRepeat);
    </script>
</x-app-layout> 