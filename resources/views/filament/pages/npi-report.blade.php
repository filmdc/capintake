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
                Export CSV
            </x-filament::button>
        @endif
    </div>

    @if($this->reportData)
        <div class="mt-6 space-y-6">
            {{-- Main NPI Table --}}
            <x-filament::section>
                <x-slot name="heading">
                    NPI Results: {{ $this->startDate }} to {{ $this->endDate }}
                </x-slot>

                <div class="overflow-hidden rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="w-20 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">NPI Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Goal / Indicator</th>
                                    <th class="w-24 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">I. Served</th>
                                    <th class="w-20 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">II. Target</th>
                                    <th class="w-20 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">III. Results</th>
                                    <th class="w-20 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">IV. % Ach.</th>
                                    <th class="w-20 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">V. Accuracy</th>
                                    <th class="w-24 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Services</th>
                                    <th class="w-24 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Value ($)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-950/5 dark:divide-white/5">
                                @foreach($this->reportData as $goal)
                                    <tr class="bg-gray-50 dark:bg-white/5">
                                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">Goal {{ $goal['goal_number'] }}</td>
                                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $goal['goal_name'] }}</td>
                                        <td class="px-4 py-3 text-right font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($goal['goal_total_clients']) }}</td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                        <td class="px-4 py-3"></td>
                                    </tr>
                                    @foreach($goal['indicators'] as $indicator)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                            <td class="px-4 py-3 pl-8 text-gray-400 dark:text-gray-500">{{ $indicator['indicator_code'] }}</td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $indicator['indicator_name'] }}</td>
                                            <td class="px-4 py-3 text-right tabular-nums {{ ($indicator['individuals_served'] ?? $indicator['unduplicated_clients']) > 0 ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-300 dark:text-gray-600' }}">
                                                {{ number_format($indicator['individuals_served'] ?? $indicator['unduplicated_clients']) }}
                                            </td>
                                            <td class="px-4 py-3 text-right tabular-nums {{ ($indicator['target'] ?? 0) > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
                                                {{ number_format($indicator['target'] ?? 0) }}
                                            </td>
                                            <td class="px-4 py-3 text-right tabular-nums {{ ($indicator['actual_results'] ?? 0) > 0 ? 'font-semibold text-green-700 dark:text-green-400' : 'text-gray-300 dark:text-gray-600' }}">
                                                {{ number_format($indicator['actual_results'] ?? 0) }}
                                            </td>
                                            <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">
                                                {{ ($indicator['pct_achieving'] ?? 0) > 0 ? number_format($indicator['pct_achieving'], 1) . '%' : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">
                                                {{ ($indicator['target_accuracy'] ?? 0) > 0 ? number_format($indicator['target_accuracy'], 1) . '%' : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-right tabular-nums {{ $indicator['total_services'] > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
                                                {{ number_format($indicator['total_services']) }}
                                            </td>
                                            <td class="px-4 py-3 text-right tabular-nums {{ $indicator['total_value'] > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
                                                ${{ number_format($indicator['total_value'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-950/10 bg-gray-50 font-bold dark:border-white/10 dark:bg-white/5">
                                    <td class="px-4 py-3 text-gray-900 dark:text-white"></td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">GRAND TOTAL (Unduplicated)</td>
                                    <td class="px-4 py-3 text-right tabular-nums text-gray-900 dark:text-white">{{ number_format($this->grandTotal) }}</td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </x-filament::section>

            {{-- Demographic Breakdown --}}
            <x-filament::section collapsible collapsed>
                <x-slot name="heading">Demographic Breakdown by Indicator</x-slot>
                <x-slot name="description">Unduplicated individuals by race, gender, and age range</x-slot>

                @php
                    $raceLabels = \App\Services\Lookup::options('race');
                    $genderLabels = \App\Services\Lookup::options('gender');
                    $ageLabels = ['0-5', '6-13', '14-17', '18-24', '25-44', '45-54', '55-59', '60-64', '65-74', '75+'];
                @endphp

                @php
                    $anyDemographicData = collect($this->reportData)->contains(fn ($g) => collect($g['indicators'])->sum('unduplicated_clients') > 0);
                @endphp

                @if(!$anyDemographicData)
                    <p class="text-sm italic text-gray-500 dark:text-gray-400">No demographic data to display — no clients matched any NPI indicator in the selected date range.</p>
                @endif

                @foreach($this->reportData as $goal)
                    @php
                        $hasData = collect($goal['indicators'])->sum('unduplicated_clients') > 0;
                    @endphp

                    @if($hasData)
                        <div class="mb-6">
                            <h3 class="mb-3 text-sm font-bold text-gray-900 dark:text-white">
                                Goal {{ $goal['goal_number'] }}: {{ $goal['goal_name'] }}
                            </h3>

                            <div class="overflow-hidden rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-50 dark:bg-white/5">
                                            <tr>
                                                <th class="px-3 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400" style="min-width: 140px;">Indicator</th>
                                                <th class="px-2 py-2.5 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                                                @foreach($raceLabels as $label)
                                                    <th class="px-1.5 py-2.5 text-right text-xs font-medium text-gray-400 dark:text-gray-500" title="{{ $label }}">{{ Str::limit($label, 8) }}</th>
                                                @endforeach
                                                <th class="border-l border-gray-200 px-1.5 py-2.5 text-right text-xs font-medium text-gray-400 dark:border-white/10 dark:text-gray-500">M</th>
                                                <th class="px-1.5 py-2.5 text-right text-xs font-medium text-gray-400 dark:text-gray-500">F</th>
                                                <th class="px-1.5 py-2.5 text-right text-xs font-medium text-gray-400 dark:text-gray-500">NB</th>
                                                @foreach($ageLabels as $age)
                                                    <th class="px-1.5 py-2.5 text-right text-xs font-medium text-gray-400 dark:text-gray-500 {{ $loop->first ? 'border-l border-gray-200 dark:border-white/10' : '' }}">{{ $age }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-950/5 dark:divide-white/5">
                                            @foreach($goal['indicators'] as $indicator)
                                                @if($indicator['unduplicated_clients'] > 0)
                                                    <tr>
                                                        <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300">{{ $indicator['indicator_code'] }}</td>
                                                        <td class="px-2 py-2.5 text-right font-semibold text-gray-900 dark:text-white">{{ $indicator['unduplicated_clients'] }}</td>
                                                        @foreach(array_keys($raceLabels) as $raceKey)
                                                            <td class="px-1.5 py-2.5 text-right tabular-nums {{ ($indicator['by_race'][$raceKey] ?? 0) > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
                                                                {{ $indicator['by_race'][$raceKey] ?? 0 }}
                                                            </td>
                                                        @endforeach
                                                        @foreach(['male', 'female', 'non_binary'] as $gKey)
                                                            <td class="px-1.5 py-2.5 text-right tabular-nums {{ $loop->first ? 'border-l border-gray-200 dark:border-white/10' : '' }} {{ ($indicator['by_gender'][$gKey] ?? 0) > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
                                                                {{ $indicator['by_gender'][$gKey] ?? 0 }}
                                                            </td>
                                                        @endforeach
                                                        @foreach($ageLabels as $ageKey)
                                                            <td class="px-1.5 py-2.5 text-right tabular-nums {{ $loop->first ? 'border-l border-gray-200 dark:border-white/10' : '' }} {{ ($indicator['by_age'][$ageKey] ?? 0) > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600' }}">
                                                                {{ $indicator['by_age'][$ageKey] ?? 0 }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
