@use('App\Icons\Android')

<native:scroll-view class="w-full h-full bg-theme-background">
    <native:column class="w-full p-4 gap-4">

        <native:text class="text-2xl font-extrabold tracking-tight text-theme-on-background">
            Popular last 24 hours
        </native:text>
        <native:text class="text-2xl font-extrabold tracking-tight text-theme-on-background">
            Explore
        </native:text>

        <native:text class="text-sm leading-relaxed text-theme-on-surface-variant">
            Videos published in the last 24 hours, ranked by views.
        </native:text>

        <native:select
            label="Region"
            :options="$this->regionNames()"
            native:model="region"
        />

        @forelse ($this->videos as $video)
            <native:pressable
                :key="'video-'.$video['id']"
                @tap="openVideo('{{ $video['id'] }}')"
                class="w-full rounded-2xl bg-theme-surface p-3 gap-3 border border-theme-outline-variant"
            >
                <native:stack class="w-full">
                    <native:image
                        :src="$video['thumbnail']"
                        :fit="2"
                        class="w-full aspect-video rounded-xl"
                        :alt="$video['title']"
                    />
                    <native:column class="w-full h-full items-center justify-center">
                        <native:icon :android="Android::PlayCircleFilled" :size="56" class="text-white/90" />
                    </native:column>
                </native:stack>

                <native:text class="text-lg font-bold text-theme-on-surface">
                    {{ $video['title'] }}
                </native:text>

                <native:column class="gap-1">
                    <native:text class="text-sm text-theme-on-surface-variant">
                        {{ $video['channel'] }}
                    </native:text>
                    <native:text class="text-xs text-theme-on-surface-variant">
                        Published {{ $video['publishedAt'] }}
                    </native:text>
                </native:column>

                <native:row class="flex-wrap gap-2">
                    <native:badge :label="number_format($video['views']).' Views'" />
                    <native:badge :label="number_format($video['likes']).' Likes'" variant="primary" />
                    <native:badge :label="number_format($video['comments']).' Comments'" variant="accent" />
                </native:row>

            </native:pressable>
        @empty
            <native:column class="items-center gap-4 py-16">
                <native:icon :android="Android::Explore" :size="56" class="text-theme-on-surface-variant" />
                <native:text class="text-center text-theme-on-surface-variant">
                    No videos were found for this region in the last 24 hours.
                </native:text>
            </native:column>
        @endforelse

    </native:column>
</native:scroll-view>