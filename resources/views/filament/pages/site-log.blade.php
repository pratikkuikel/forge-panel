<x-filament-panels::page>
    <x-filament::section style="min-width: 0; max-width: 100%; overflow: hidden;">
        <x-slot name="heading">
            {{ $siteDetails['name'] }}
        </x-slot>

        <x-slot name="description">
            Server {{ $server }} · Site {{ $site }}
        </x-slot>

        <div
            aria-label="Application log"
            role="region"
            tabindex="0"
            style="
                contain: inline-size;
                width: 100%;
                max-width: 100%;
                min-width: 0;
                min-height: 24rem;
                max-height: 70vh;
                overflow: auto;
                overscroll-behavior: contain;
                scrollbar-gutter: stable;
                -webkit-overflow-scrolling: touch;
                border-radius: 0.75rem;
                background: #030712;
                color: #f3f4f6;
                box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.1);
            "
        >
            <pre style="
                box-sizing: border-box;
                width: max-content;
                min-width: 100%;
                margin: 0;
                padding: 1rem;
                white-space: pre;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
                font-size: 0.875rem;
                line-height: 1.5rem;
            ">{{ $log !== '' ? $log : 'The application log is empty.' }}</pre>
        </div>
    </x-filament::section>
</x-filament-panels::page>
