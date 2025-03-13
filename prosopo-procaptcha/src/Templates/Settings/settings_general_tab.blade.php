{!! $form !!}

<div class="flex flex-col gap-5 ml-12 max-w-80">
    <p class="text-sm">
        {{ __('Preview: if the credentials are valid, you should be able to complete the captcha below:', 'prosopo-procaptcha') }}
    </p>

    {!! $preview !!}
</div>