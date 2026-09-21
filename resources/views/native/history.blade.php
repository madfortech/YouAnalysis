@use('App\Icons\Android')

<native:scroll-view class="w-full h-full bg-theme-background">
    <native:column class="w-full p-4 gap-4">

        <native:text class="text-2xl font-extrabold tracking-tight text-theme-on-background">
            Analysis history
        </native:text>

        @forelse ($this->history() as $entry)
            <native:column class="w-full rounded-2xl bg-theme-surface p-4 gap-3 border border-theme-outline-variant">

                <native:row class="items-center justify-between">
                    <native:text class="text-lg font-bold text-theme-on-surface">
                        {{ $entry['topic'] }}
                    </native:text>
                    <native:text class="text-xs text-theme-on-surface-variant">
                        {{ $entry['time'] }}
                    </native:text>
                </native:row>

                <native:divider class="w-full" />

                @foreach ($this->parsedLines($entry['result']) as $row)
                    <native:column class="gap-1">
                        <native:text class="text-xs font-bold uppercase tracking-wider text-theme-primary">
                            {{ $row['label'] }}
                        </native:text>

                        @if ($row['items'] !== [])
                            <native:row class="flex-wrap gap-2">
                                @foreach ($row['items'] as $item)
                                    <native:badge :label="$item" variant="primary" />
                                @endforeach
                            </native:row>
                        @else
                            <native:text class="text-sm leading-relaxed text-theme-on-surface">
                                {{ $row['value'] }}
                            </native:text>
                        @endif
                    </native:column>
                @endforeach

            </native:column>
        @empty
            <native:column class="items-center gap-4 py-16">
                <native:icon :android="Android::History" :size="56" class="text-theme-on-surface-variant" />
                <native:text class="text-center text-theme-on-surface-variant">
                    No analysis history yet. Run an AI analysis first.
                </native:text>
                <native:button label="Go to AI Analysis" @tap="navigate('/ai-analysis')" />
            </native:column>
        @endforelse

    </native:column>
</native:scroll-view>