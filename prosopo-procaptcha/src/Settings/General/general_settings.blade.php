{!! $form !!}

<div class="flex flex-col gap-5 ml-12 max-w-80">
    <p class="text-sm">
        {{ __('Preview: if the credentials are valid, you should be able to complete the captcha below:', 'prosopo-procaptcha') }}
    </p>

    {!! $widget_preview !!}

    <div class="mt-3 flex justify-center bg-white rounded p-5">
        {!! $tier_upgrade_banner !!}
    </div>
</div>