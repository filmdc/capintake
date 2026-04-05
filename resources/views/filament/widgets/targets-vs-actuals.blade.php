<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">FNPI Targets vs. Actuals (FY {{ $this->fiscalYear }})</x-slot>

        @if(empty($this->indicators))
            <p class="text-sm italic text-gray-500 dark:text-gray-400">No FNPI targets set for FY {{ $this->fiscalYear }}. Set targets on the FNPI Targets page.</p>
        @else
            <div class="overflow-hidden rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Indicator</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Served</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Target</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Achieved</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Progress</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-950/5 dark:divide-white/5">
                            @foreach($this->indicators as $ind)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ $ind['indicator_code'] }}</span>
                                        <span class="ml-1.5 text-gray-700 dark:text-gray-300">{{ Str::limit($ind['indicator_name'], 40) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">{{ number_format($ind['served']) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">{{ number_format($ind['target']) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums font-semibold text-gray-900 dark:text-white">{{ number_format($ind['actual']) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">{{ number_format($ind['pct_of_target'], 1) }}%</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $color = match($ind['status']) {
                                                'on_track' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                                'at_risk' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                                                'behind' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400',
                                            };
                                            $label = match($ind['status']) {
                                                'on_track' => 'On Track',
                                                'at_risk' => 'At Risk',
                                                'behind' => 'Behind',
                                                default => 'No Target',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $color }}">{{ $label }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
