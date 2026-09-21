@use('App\Icons\Android')

<native:scroll-view class="w-full h-full bg-theme-background">
    <native:column class="w-full p-4 gap-4">

        @if ($this->video() !== [])
            <native:column class="w-full rounded-2xl bg-theme-surface p-3 gap-3 border border-theme-outline-variant">

                <native:webview
                    :src="'https://www.youtube.com/watch?v='.$this->param('id')"
                    javascript
                    dom-storage
                    class="w-full aspect-video rounded-xl overflow-hidden"
                />

                <native:text class="text-lg font-bold text-theme-on-surface">
                    {{ $this->video()['title'] }}
                </native:text>

                <native:column class="gap-1">
                    <native:text class="text-sm text-theme-on-surface-variant">
                        {{ $this->video()['channel'] }}
                    </native:text>
                    <native:text class="text-xs text-theme-on-surface-variant">
                        Published {{ $this->video()['publishedAt'] }}
                    </native:text>
                </native:column>

                <native:row class="flex-wrap gap-2">
                    <native:badge :label="number_format($this->video()['views']).' Views'" />
                    <native:badge :label="number_format($this->video()['likes']).' Likes'" variant="primary" />
                    <native:badge :label="number_format($this->video()['comments']).' Comments'" variant="accent" />
                </native:row>

            </native:column>
        @else
            <native:column class="items-center gap-4 py-16">
                <native:icon :android="Android::SmartDisplay" :size="56" class="text-theme-on-surface-variant" />
                <native:text class="text-center text-theme-on-surface-variant">
                    No video selected.
                </native:text>
            </native:column>
        @endif

    </native:column>
</native:scroll-view>