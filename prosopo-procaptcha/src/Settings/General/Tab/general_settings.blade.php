{!! $form !!}

<general-procaptcha-settings class="flex flex-col gap-5 ml-12 max-w-96">
    @if (! $has_keys)
        <div class="flex flex-col gap-3 rounded-lg p-5 bg-navy text-white">
            <p class="m-0 text-base font-semibold text-white">
                {{ __( 'Get your free site key', 'prosopo-procaptcha' ) }}
            </p>
            <ol class="m-0 pl-5 flex flex-col gap-1 list-decimal text-white/85">
                <li>{{ __( 'Sign up for a free Prosopo account.', 'prosopo-procaptcha' ) }}</li>
                <li>{{ __( 'Copy your Site Key and Secret Key from the Prosopo portal.', 'prosopo-procaptcha' ) }}</li>
                <li>{{ __( 'Paste them here, save, and turn on the forms you want to protect.', 'prosopo-procaptcha' ) }}</li>
            </ol>
            <a target="_blank" rel="noreferrer"
               href="https://prosopo.io/register/?utm_source=procaptcha-wp&amp;utm_medium=plugin&amp;utm_campaign=settings&amp;utm_content=get-keys"
               class="self-start flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded text-white bg-pink no-underline transition hover:bg-pink-dark">
                <span>{{ __( 'Get your free site key', 'prosopo-procaptcha' ) }}</span>
                <span class="icon-[material-symbols--arrow-forward] w-5 h-5"></span>
            </a>
            <p class="m-0 text-xs text-white/70">
                {{ __( 'Free for 10,000 requests a month. No credit card.', 'prosopo-procaptcha' ) }}
            </p>
        </div>
    @endif

    <p class="m-0 text-sm">
        {{ __('Preview: if the credentials are valid, you should be able to complete the captcha below:', 'prosopo-procaptcha') }}
    </p>

    {{--dummy form to avoid "form not found" console error from the procaptcha script, confusing users--}}
    <form class="dummy">
        {!! $widget_preview !!}
    </form>

    <div class="flex flex-col gap-3 rounded-lg p-5 bg-white">
        <p class="m-0 text-base font-semibold text-navy">
            {{ __( 'What protects your forms', 'prosopo-procaptcha' ) }}
        </p>
        <ul class="m-0 flex flex-col gap-2">
            <li class="flex gap-2">
                <span class="icon-[material-symbols--verified-user-outline] w-5 h-5 bg-blue shrink-0"></span>
                <span>{{ __( 'An invisible check: most visitors see nothing to solve, and only visitors who look like bots get a challenge.', 'prosopo-procaptcha' ) }}</span>
            </li>
            <li class="flex gap-2">
                <span class="icon-[material-symbols--bug-report-outline] w-5 h-5 bg-blue shrink-0"></span>
                <span>{{ __( 'A hidden honeypot trap, and blocking of known abusive networks.', 'prosopo-procaptcha' ) }}</span>
            </li>
            <li class="flex gap-2">
                <span class="icon-[material-symbols--policy-outline] w-5 h-5 bg-blue shrink-0"></span>
                <span>{{ __( 'No cookies and no tracking.', 'prosopo-procaptcha' ) }}</span>
            </li>
        </ul>
        <p class="m-0 text-xs text-gray">
            {{ __( 'All included on the free plan.', 'prosopo-procaptcha' ) }}
            <a class="text-blue" target="_blank" rel="noreferrer"
               href="https://prosopo.io/data-processing-agreement/?utm_source=procaptcha-wp&amp;utm_medium=plugin&amp;utm_campaign=settings&amp;utm_content=dpa">
                {{ __( 'Data processing agreement', 'prosopo-procaptcha' ) }}
            </a>
        </p>
    </div>

    <div class="general-procaptcha-settings__tier-upgrade mt-3 flex justify-center bg-white rounded-lg p-5 opacity-0 transition-all transition-duration-500
    data-visible:opacity-100">
        {!! $tier_upgrade_banner !!}
    </div>
</general-procaptcha-settings>
