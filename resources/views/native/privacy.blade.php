@use('App\Icons\Android')

<native:scroll-view class="w-full h-full bg-theme-background">
    <native:column class="w-full p-4 gap-4">

        <native:text class="text-2xl font-extrabold tracking-tight text-theme-on-background">
            Privacy Policy
        </native:text>

        <native:column class="w-full rounded-2xl bg-theme-surface p-4 gap-3 border border-theme-outline-variant">
            <native:icon :android="Android::PrivacyTip" :size="28" class="text-theme-primary" />
            <native:text class="text-sm leading-relaxed text-theme-on-surface">
                This Privacy Policy explains how You Analysis collects, uses, and protects your information.
            </native:text>
        </native:column>

        @foreach ($this->items() as $index => $item)
            <native:column class="w-full rounded-2xl bg-theme-surface p-4 gap-2 border border-theme-outline-variant">
                <native:row class="items-center gap-3">
                    <native:badge :label="(string) ($index + 1)" variant="primary" />
                    <native:text class="text-sm leading-relaxed text-theme-on-surface flex-1">
                        {{ $item }}
                    </native:text>
                </native:row>
            </native:column>
        @endforeach

    </native:column>
</native:scroll-view>