<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            {{ $siteDetails['name'] }}
        </x-slot>

        <x-slot name="description">
            Server {{ $server }} · Site {{ $site }}
        </x-slot>

        <div class="overflow-x-auto rounded-xl bg-gray-950 p-4 shadow-inner ring-1 ring-white/10">
            <pre class="min-h-96 whitespace-pre-wrap break-words font-mono text-sm leading-6 text-gray-100">{{ $log !== '' ? $log : 'The application log is empty.' }}</pre>
        </div>
    </x-filament::section>
</x-filament-panels::page>
