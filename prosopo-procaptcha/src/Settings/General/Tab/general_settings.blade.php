{!! $form !!}

<general-procaptcha-settings class="flex flex-col gap-5 ml-12 max-w-80">
    <p class="text-sm">
        {{ __('Preview: if the credentials are valid, you should be able to complete the captcha below:', 'prosopo-procaptcha') }}
    </p>

    {{--dummy form to avoid "form not found" console error from the procaptcha script, confusing users--}}
    <form class="dummy">
        {!! $widget_preview !!}
    </form>

    <div class="general-procaptcha-settings__tier-upgrade mt-3 flex justify-center bg-white rounded p-5 opacity-0 transition-all transition-duration-500
    data-visible:opacity-100">
        {!! $tier_upgrade_banner !!}
    </div>
</general-procaptcha-settings>