<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome to CAPIntake</h2>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Let's get your agency set up. This wizard will guide you through the initial configuration.</p>
        </div>

        <form wire:submit="submit">
            {{ $this->form }}
        </form>
    </div>
</x-filament-panels::page>
