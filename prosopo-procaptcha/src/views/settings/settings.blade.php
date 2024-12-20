@use('Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\Typed')

<prosopo-procaptha-wp-settings style="display:block;margin-left:-18px;">
    <template shadowrootmode="open">
        <style>
            {!! $css !!}
        </style>

        <div class="mt-6 text-sm font-sans px-5">
            <div class="flex items-start gap-4">
                <h1 class="text-[23px]">
                    {{ __( 'Prosopo Procaptcha', 'prosopo-procaptcha' ) }}
                </h1>
                <a class="text-[13px] -mt-1 text-blue underline transition
                hover:text-black" href="https://prosopo.io/" target="_blank">
                    {{ __( 'Visit Website', 'prosopo-procaptcha' ) }}
                </a>
                <a class="text-[13px] -mt-1 text-blue underline transition
                hover:text-black" href="https://docs.prosopo.io/en/wordpress-plugin/"
                   target="_blank">
                    {{ __( 'Open Docs', 'prosopo-procaptcha' ) }}
                </a>
                <a class="text-[13px] -mt-1 text-blue underline transition
                hover:text-black" href="https://portal.prosopo.io/"
                   target="_blank">
                    {{ __( 'Visit Portal', 'prosopo-procaptcha' ) }}
                </a>
            </div>
            <div class="mt-6">
                <span>
                    {{ __( 'GDPR compliant, privacy friendly and better value captcha.', 'prosopo-procaptcha' ) }}
                </span>
            </div>

            <ul class="flex leading-none my-5 -mx-5 bg-white">
                @foreach ($tabs as $tab)
                    <li class="m-0 border-r border-[#dde8f2] last:border-r-0">
                        <a href="{{ Typed::string($tab, 'url') }}"
                                @class([
                                    'block font-medium tracking-wide py-5 px-8 transition hover:text-black',
                                    'text-black' => Typed::bool($tab, 'is_active'),
                                    'text-blue'=> false === Typed::bool($tab, 'is_active'),
                                ])>
                            {{ Typed::string($tab, 'title') }}
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($is_just_saved)
                <div class="mt-7 text-green-700">
                    {{ __( 'Settings successfully saved.', 'prosopo-procaptcha' ) }}
                </div>
            @endif

            <div class="mt-7 flex gap-12">
                {!! $tab_content !!}
            </div>
        </div>

        @if ($js_file)
            <script type="module">
				window.prosopoProcaptchaWpSettings = {!! json_encode($js_data) !!};
            </script>
            <script type="module" async src="{{ $js_file }}"></script>
        @endif
    </template>
</prosopo-procaptha-wp-settings>