@use('App\Icons\Android')

<native:scroll-view class="w-full h-full bg-theme-background">
    <native:column class="w-full p-6 gap-6">

        <native:column class="items-center gap-2 pt-8">
            <native:icon :android="Android::Insights" :size="42" class="text-theme-primary" />
            <native:text class="text-4xl font-extrabold tracking-tight text-center text-theme-on-background">
                You Analysis
            </native:text>
            <native:text class="text-sm text-center leading-relaxed text-theme-on-surface-variant">
                AI-powered platform built using the YouTube API. Analyze channels, videos, trends, engagement, and audience insights with intelligent AI-driven reports.
            </native:text>
            <native:text class="text-xs text-theme-on-surface-variant">
                Independent project · Not affiliated with YouTube
            </native:text>
        </native:column>

        @if (! $this->renderedAsGuest())
            <native:button
                label="Continue as Guest"
                @tap="continueAsGuest"
                class="w-full mt-4"
            />
        @else
            <native:column class="w-full gap-3 mt-2">

                <native:list-item
                    headline="Start a new analysis"
                    supporting="AI Analysis"
                    @tap="navigate('/ai-analysis')"
                />

                <native:list-item
                    headline="Explore popular videos"
                    supporting="What's trending in the last 24 hours"
                    @tap="navigate('/popular')"
                />

                <native:list-item
                    headline="View history"
                    supporting="Your previous AI reports"
                    @tap="navigate('/history')"
                />

                <native:list-item
                    headline="Logout"
                    supporting="Leave the guest session"
                    @tap="logout"
                />

            </native:column>
        @endif

        <native:divider class="w-full" />

        <native:column class="w-full gap-3">
            <native:text class="text-sm font-semibold text-theme-on-surface-variant">
                Information
            </native:text>

            <native:list-item
                headline="Privacy Policy"
                @tap="navigate('/privacy')"
            />

            <native:list-item
                headline="Terms of Service"
                @tap="navigate('/terms')"
            />

            @if ($this->renderedAsGuest())
                <native:list-item
                    headline="Delete Everything"
                    supporting="Permanently remove session data from this device"
                    @tap="navigate('/confirm-logout')"
                />
            @endif
        </native:column>

    </native:column>
</native:scroll-view>