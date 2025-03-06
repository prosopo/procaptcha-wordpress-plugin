@use('function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string')
@use('function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool')

<prosopo-procaptha-wp-settings style="display:block;margin-left:-18px;">
    <template shadowrootmode="open">
        @foreach($style_asset_urls as $style_asset_url)
            <link rel="stylesheet" href="{{ $style_asset_url }}">
        @endforeach

        <div class="mt-5 text-sm font-sans px-5">
            <div class="flex items-start gap-4">
                <h1 class="text-[23px]">
                    {{ __( 'Prosopo Procaptcha', 'prosopo-procaptcha' ) }}
                </h1>
                <a class="text-[13px] mt-1 text-blue underline transition
                hover:text-black" href="https://prosopo.io/" target="_blank">
                    {{ __( 'Visit Website', 'prosopo-procaptcha' ) }}
                </a>
                <a class="text-[13px] mt-1 text-blue underline transition
                hover:text-black" href="https://docs.prosopo.io/en/wordpress-plugin/"
                   target="_blank">
                    {{ __( 'Open Docs', 'prosopo-procaptcha' ) }}
                </a>
                <a class="text-[13px] mt-1 text-blue underline transition
                hover:text-black" href="https://portal.prosopo.io/"
                   target="_blank">
                    {{ __( 'Visit Portal', 'prosopo-procaptcha' ) }}
                </a>
            </div>
            <div class="mt-4">
                <span>
                    {{ __( 'GDPR compliant, privacy friendly and better value captcha.', 'prosopo-procaptcha' ) }}
                </span>
            </div>

            <ul class="flex leading-none my-5 -mx-5 bg-white">
                @foreach ($tabs as $tab)
                    <li class="m-0 border-solid border-r border-[#dde8f2] last:border-r-0">
                        <a href="{{ string($tab, 'url') }}"
                                @class([
                                    'block font-medium tracking-wide py-5 px-8 transition hover:text-black',
                                    'text-black' => bool($tab, 'is_active'),
                                    'text-blue'=> false === bool($tab, 'is_active'),
                                ])>
                            {{ string($tab, 'title') }}
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
    </template>
</prosopo-procaptha-wp-settings>