@use('App\Icons\Android')

<native:scroll-view class="w-full h-full bg-theme-background">
    <native:column class="w-full p-4 gap-4">

        <native:text class="text-2xl font-extrabold tracking-tight text-theme-on-background">
            AI Channel Analysis
        </native:text>
        <native:text class="text-sm leading-relaxed text-theme-on-surface-variant">
            Enter a topic, pick a trend period, and let AI analyze the videos, competition, and growth from that range.
        </native:text>

        <native:outlined-text-input
            label="Topic"
            placeholder="e.g. Cricket matches analysis"
            multiline
            min-lines="4"
            max-lines="8"
            native:model="topic"
            @change="onTopicChanged"
            :supporting="''.$this->wordCount().'/155 words'"
        />

        <native:column class="w-full gap-2">
            <native:text class="text-xs font-bold uppercase tracking-wider text-theme-on-surface-variant">
                Trend period
            </native:text>
            <native:column class="w-full gap-2">
                <native:button
                    :label="'Recent/Current Trend'"
                    :variant="($this->timeRange === '3_months') ? 'primary' : 'secondary'"
                    @tap="setTimeRange('3_months')"
                    class="w-full"
                />
                <native:button
                    :label="'Yearly Trend + Seasonality'"
                    :variant="($this->timeRange === '1_year') ? 'primary' : 'secondary'"
                    @tap="setTimeRange('1_year')"
                    class="w-full"
                />
                <native:button
                    :label="'Long-Term Historical Trend'"
                    :variant="($this->timeRange === '5_years') ? 'primary' : 'secondary'"
                    @tap="setTimeRange('5_years')"
                    class="w-full"
                />
            </native:column>
        </native:column>

        @if ($this->error)
            <native:badge
                :label="$this->error"
                variant="destructive"
            />
        @endif

        <native:row class="w-full gap-3">
            <native:button
                label="Analyze"
                :disabled="$this->analyzing"
                @tap="analyze"
                class="flex-1"
            />
            <native:button
                label="History"
                variant="secondary"
                @tap="openHistory"
            />
        </native:row>

        @if ($this->analyzing)
            <native:column class="items-center gap-3 py-16">
                <native:activity-indicator/>
                <native:text class="text-theme-on-surface-variant">
                    Analyzing {{ $this->topic ?: 'topic' }}...
                </native:text>
            </native:column>
        @endif

        @if ($this->result)
            <native:column class="w-full gap-4">

                <native:column class="w-full gap-2">
                    <native:row class="items-center gap-2">
                        <native:icon :android="Android::Insights" :size="22" class="text-theme-primary" />
                        <native:text class="text-xl font-bold text-theme-on-background">
                            Analysis result
                        </native:text>
                    </native:row>
                    <native:button
                        label="View history"
                        variant="secondary"
                        @tap="openHistory"
                    />
                </native:column>

                @foreach ($this->parsedResult() as $row)
                    <native:column class="w-full rounded-2xl bg-theme-surface p-4 gap-3 border border-theme-outline-variant">
                        <native:text class="text-xs font-bold uppercase tracking-wider text-theme-primary">
                            {{ $row['label'] }}
                        </native:text>

                        @if ($row['items'] !== [])
                            <native:row class="flex-wrap gap-2">
                                @foreach ($row['items'] as $item)
                                    <native:badge>
                                        {{ $item }}
                                    </native:badge>
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
        @endif

    </native:column>
</native:scroll-view>