<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">My Follow-Ups</x-slot>

        @if(!empty($this->overdue))
            <div class="mb-5">
                <h4 class="mb-2.5 text-sm font-semibold text-red-600 dark:text-red-400">Overdue ({{ count($this->overdue) }})</h4>
                <div class="space-y-2">
                    @foreach($this->overdue as $item)
                        <div class="flex items-center justify-between rounded-lg bg-red-50 px-4 py-3 ring-1 ring-red-200 dark:bg-red-500/10 dark:ring-red-500/20">
                            <div>
                                <a href="{{ \App\Filament\Resources\ClientResource::getUrl('edit', ['record' => $item['client_id']]) }}" class="font-medium text-red-700 hover:underline dark:text-red-300">{{ $item['client_name'] }}</a>
                                <span class="ml-2 text-sm text-red-600 dark:text-red-400">{{ $item['type'] }}</span>
                            </div>
                            <span class="shrink-0 text-xs font-medium text-red-500 dark:text-red-400">{{ $item['days_overdue'] }} days overdue</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($this->upcoming))
            <div>
                <h4 class="mb-2.5 text-sm font-semibold text-gray-600 dark:text-gray-400">Upcoming (Next 7 Days)</h4>
                <div class="space-y-2">
                    @foreach($this->upcoming as $item)
                        <div class="flex items-center justify-between rounded-lg px-4 py-3 ring-1 ring-gray-950/5 dark:ring-white/10">
                            <div>
                                <a href="{{ \App\Filament\Resources\ClientResource::getUrl('edit', ['record' => $item['client_id']]) }}" class="font-medium text-primary-600 hover:underline dark:text-primary-400">{{ $item['client_name'] }}</a>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $item['type'] }}</span>
                            </div>
                            <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">{{ $item['scheduled_date'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(empty($this->overdue) && empty($this->upcoming))
            <p class="text-sm italic text-gray-500 dark:text-gray-400">No upcoming or overdue follow-ups.</p>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
