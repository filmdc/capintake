<x-filament-panels::page>
    {{ $this->form }}

    <div class="flex flex-wrap gap-3 mt-4">
        <x-filament::button wire:click="generateReport" icon="heroicon-o-play">
            Generate Report
        </x-filament::button>

        @if($this->reportData)
            <x-filament::button wire:click="exportPdf" color="danger" icon="heroicon-o-document-arrow-down">
                Export PDF
            </x-filament::button>

            <x-filament::button wire:click="exportCsv" color="success" icon="heroicon-o-table-cells">
                Export Excel
            </x-filament::button>
        @endif
    </div>

    @if($this->reportData)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">
                    NPI Results: {{ $this->startDate }} to {{ $this->endDate }}
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                                <th class="text-left py-2 px-3 font-semibold text-gray-600 dark:text-gray-400" style="width: 80px;">NPI Code</th>
                                <th class="text-left py-2 px-3 font-semibold text-gray-600 dark:text-gray-400">Goal / Indicator</th>
                                <th class="text-right py-2 px-3 font-semibold text-gray-600 dark:text-gray-400" style="width: 120px;">Unduplicated</th>
                                <th class="text-right py-2 px-3 font-semibold text-gray-600 dark:text-gray-400" style="width: 100px;">Services</th>
                                <th class="text-right py-2 px-3 font-semibold text-gray-600 dark:text-gray-400" style="width: 100px;">Value ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->reportData as $goal)
                                <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-2.5 px-3 font-bold text-gray-900 dark:text-white">Goal {{ $goal['goal_number'] }}</td>
                                    <td class="py-2.5 px-3 font-bold text-gray-900 dark:text-white">{{ $goal['goal_name'] }}</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-gray-900 dark:text-white">{{ number_format($goal['goal_total_clients']) }}</td>
                                    <td class="py-2.5 px-3"></td>
                                    <td class="py-2.5 px-3"></td>
                                </tr>
                                @foreach($goal['indicators'] as $indicator)
                                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                        <td class="py-2 px-3 pl-6 text-gray-500 dark:text-gray-400">{{ $indicator['indicator_code'] }}</td>
                                        <td class="py-2 px-3 text-gray-700 dark:text-gray-300">{{ $indicator['indicator_name'] }}</td>
                                        <td class="py-2 px-3 text-right {{ $indicator['unduplicated_clients'] > 0 ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-600' }}">
                                            {{ number_format($indicator['unduplicated_clients']) }}
                                        </td>
                                        <td class="py-2 px-3 text-right {{ $indicator['total_services'] > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600' }}">
                                            {{ number_format($indicator['total_services']) }}
                                        </td>
                                        <td class="py-2 px-3 text-right {{ $indicator['total_value'] > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600' }}">
                                            ${{ number_format($indicator['total_value'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <tr class="bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-bold">
                                <td class="py-3 px-3"></td>
                                <td class="py-3 px-3">GRAND TOTAL (Unduplicated)</td>
                                <td class="py-3 px-3 text-right">{{ number_format($this->grandTotal) }}</td>
                                <td class="py-3 px-3"></td>
                                <td class="py-3 px-3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
