<x-filament-panels::page>
    <div class="flex flex-wrap items-center gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Fiscal Year:</label>
        <select wire:model.live="fiscalYear" class="fi-input rounded-lg border-none bg-white py-2 pe-8 ps-3 text-sm shadow-sm ring-1 ring-gray-950/10 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20">
            @for ($y = now()->year + 2; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}">FFY {{ $y }}</option>
            @endfor
        </select>

        <div class="flex gap-2 sm:ml-auto">
            <x-filament::button wire:click="copyFromPreviousYear" color="gray" icon="heroicon-o-document-duplicate" size="sm">
                Copy from Previous Year
            </x-filament::button>

            <x-filament::button wire:click="saveTargets" icon="heroicon-o-check" size="sm">
                Save All
            </x-filament::button>
        </div>
    </div>

    <div class="space-y-6">
        @php $currentGoal = null; @endphp
        @foreach($targets as $idx => $target)
            @if($target['goal_number'] !== $currentGoal)
                @php $currentGoal = $target['goal_number']; @endphp
                @if(!$loop->first)</div></div>@endif
                <div class="overflow-hidden rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10">
                    <div class="bg-gray-50 px-4 py-3 dark:bg-white/5">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            Goal {{ $target['goal_number'] }}: {{ $target['goal_name'] }}
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-950/5 dark:divide-white/5">
            @endif
            <div class="flex items-center gap-4 px-4 py-3">
                <span class="w-24 shrink-0 font-mono text-xs text-gray-400 dark:text-gray-500">{{ $target['code'] }}</span>
                <span class="flex-1 text-sm text-gray-700 dark:text-gray-300">{{ $target['name'] }}</span>
                <input
                    type="number"
                    wire:model.lazy="targets.{{ $idx }}.target"
                    min="0"
                    class="w-24 shrink-0 rounded-lg border-none bg-white py-2 text-right text-sm shadow-sm ring-1 ring-gray-950/10 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20"
                    placeholder="0"
                >
            </div>
        @endforeach
        @if($currentGoal !== null)</div></div>@endif
    </div>
</x-filament-panels::page>
