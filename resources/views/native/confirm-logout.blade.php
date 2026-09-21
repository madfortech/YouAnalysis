@use('App\Icons\Android')

<native:column class="w-full h-full p-6 justify-center gap-6 bg-theme-background">

    <native:column class="items-center gap-3">
        <native:icon :android="Android::Delete" :size="56" class="text-theme-destructive" />
        <native:text class="text-2xl font-extrabold tracking-tight text-center text-theme-on-background">
            Delete Everything?
        </native:text>
        <native:text class="text-sm leading-relaxed text-center text-theme-on-surface-variant">
            This will permanently remove your guest session and all analysis history from this device. This action cannot be undone.
        </native:text>
    </native:column>

    <native:row class="w-full gap-3">
        <native:button
            label="Cancel"
            variant="secondary"
            @tap="back"
            class="flex-1"
        />
        <native:button
            label="Delete Everything"
            variant="destructive"
            @tap="deleteEverything"
            class="flex-1"
        />
    </native:row>

</native:column>