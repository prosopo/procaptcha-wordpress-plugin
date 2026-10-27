@use('function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string')
@use('function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool')

<prosopo-procaptha-wp-settings style="display:block;margin-left:-18px;">
    <template shadowrootmode="open">
        @foreach($style_asset_urls as $style_asset_url)
            <link rel="stylesheet" href="{{ $style_asset_url }}">
        @endforeach

        <div class="mt-5 text-sm font-sans px-5">
            <div class="-mx-5 -mt-5 px-8 py-6 bg-navy text-white flex flex-wrap items-center justify-between gap-6
            bg-[radial-gradient(ellipse_at_85%_0%,rgba(235,66,126,0.35),transparent_55%),radial-gradient(ellipse_at_60%_120%,rgba(105,95,173,0.5),transparent_60%)]">
                <div class="flex items-center gap-5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2062.63 468.67" fill="currentColor" class="h-7 w-auto" role="img" aria-label="Prosopo"><path d="M335.55,1825.19A147.75,147.75,0,0,1,483.3,1972.94h50.5c0-109.49-88.76-198.25-198.25-198.25v50.5Z" transform="translate(-215.73 -1774.69)"/><path d="M269.36,1891.39A147.74,147.74,0,0,1,417.1,2039.13h50.5c0-109.49-88.75-198.24-198.24-198.24v50.5Z" transform="translate(-215.73 -1774.69)"/><path d="M414,2157.17a147.75,147.75,0,0,1-147.74-147.74h-50.5c0,109.49,88.75,198.24,198.24,198.24v-50.5Z" transform="translate(-215.73 -1774.69)"/><path d="M480.17,2091a147.74,147.74,0,0,1-147.74-147.75H281.92c0,109.49,88.76,198.25,198.25,198.25V2091Z" transform="translate(-215.73 -1774.69)"/><path d="M862.8,2017.5q-27.39,22.86-78.25,22.86h-65v112.19H654.82v-312h134q46.32,0,73.86,24.13t27.55,74.72Q890.2,1994.64,862.8,2017.5ZM813,1905.1q-12.37-10.36-34.7-10.38H719.59v91.87h58.75q22.32,0,34.7-11.22t12.39-35.56Q825.43,1915.48,813,1905.1Z" transform="translate(-215.73 -1774.69)"/><path d="M1045.69,1916.42c.78.08,2.51.19,5.19.32v61.81c-3.81-.42-7.2-.71-10.16-.85s-5.36-.21-7.2-.21q-36.4,0-48.89,23.71-7,13.33-7,41.06v110.29H916.89V1921.82h57.58V1962q14-23.07,24.34-31.54,16.94-14.18,44-14.18C1044,1916.32,1044.92,1916.35,1045.69,1916.42Z" transform="translate(-215.73 -1774.69)"/><path d="M1265.64,2124.32q-29.21,36.06-88.69,36.06t-88.69-36.06Q1059,2088.26,1059,2037.5q0-49.9,29.22-86.5t88.69-36.59q59.47,0,88.69,36.59t29.21,86.5Q1294.85,2088.26,1265.64,2124.32ZM1217.38,2091q14.17-18.81,14.18-53.48t-14.18-53.37q-14.19-18.7-40.64-18.71T1136,1984.13q-14.29,18.72-14.29,53.37T1136,2091q14.28,18.81,40.75,18.81T1217.38,2091Z" transform="translate(-215.73 -1774.69)"/><path d="M1371.81,2078.88q1.92,16.1,8.29,22.87,11.28,12.06,41.7,12.06,17.85,0,28.39-5.29t10.53-15.88a17.12,17.12,0,0,0-8.48-15.45q-8.49-5.28-63.12-18.2-39.33-9.73-55.41-24.35-16.08-14.39-16.09-41.49,0-32,25.14-54.93t70.75-23q43.26,0,70.53,17.25t31.29,59.59H1455q-1.27-11.64-6.58-18.42-10-12.27-34-12.28-19.74,0-28.13,6.14t-8.38,14.4c0,6.91,3,11.93,8.92,15q8.89,4.89,63,16.73,36,8.46,54.05,25.61,17.77,17.35,17.78,43.39,0,34.3-25.56,56t-79,21.7q-54.51,0-80.49-23t-26-58.53Z" transform="translate(-215.73 -1774.69)"/><path d="M1745.54,2124.32q-29.22,36.06-88.7,36.06t-88.69-36.06q-29.2-36.06-29.21-86.82,0-49.9,29.21-86.5t88.69-36.59q59.49,0,88.7,36.59t29.21,86.5Q1774.75,2088.26,1745.54,2124.32ZM1697.27,2091q14.19-18.81,14.19-53.48t-14.19-53.37q-14.18-18.7-40.64-18.71t-40.75,18.71q-14.28,18.72-14.28,53.37t14.28,53.48q14.3,18.81,40.75,18.81T1697.27,2091Z" transform="translate(-215.73 -1774.69)"/><path d="M1992.75,1946.59q28.24,29.84,28.23,87.63,0,61-27.58,92.93t-71.06,32q-27.69,0-46-13.76-10-7.62-19.6-22.23v120.24H1797V1921.82h57.79v34.08q9.79-15,20.88-23.71,20.23-15.43,48.15-15.45Q1964.53,1916.74,1992.75,1946.59Zm-46.3,43.39q-12.3-20.52-39.88-20.53-33.15,0-45.54,31.11-6.43,16.51-6.42,41.92,0,40.21,21.58,56.51,12.82,9.53,30.37,9.53,25.45,0,38.83-19.48t13.36-51.86Q1958.75,2010.51,1946.45,1990Z" transform="translate(-215.73 -1774.69)"/><path d="M2249.14,2124.32q-29.2,36.06-88.69,36.06t-88.69-36.06q-29.22-36.06-29.21-86.82,0-49.9,29.21-86.5t88.69-36.59q59.49,0,88.69,36.59t29.22,86.5Q2278.36,2088.26,2249.14,2124.32ZM2200.88,2091q14.19-18.81,14.18-53.48t-14.18-53.37q-14.18-18.7-40.64-18.71t-40.75,18.71q-14.28,18.72-14.29,53.37t14.29,53.48q14.3,18.81,40.75,18.81T2200.88,2091Z" transform="translate(-215.73 -1774.69)"/></svg>
                    <div class="flex flex-col gap-1 border-l border-solid border-white/25 pl-5">
                        <h1 class="m-0 p-0 text-lg font-semibold text-white leading-tight">
                            {{ __( 'Procaptcha for WordPress', 'prosopo-procaptcha' ) }}
                        </h1>
                        <p class="m-0 text-[13px] text-white/75">
                            {{ __( 'Free, GDPR-compliant CAPTCHA that stops spam bots. No cookies.', 'prosopo-procaptcha' ) }}
                        </p>
                    </div>
                </div>
                <nav class="flex items-center gap-5 text-[13px]">
                    <a class="text-white/80 no-underline transition hover:text-white" href="https://docs.prosopo.io/en/wordpress-plugin/"
                       target="_blank">
                        {{ __( 'Docs', 'prosopo-procaptcha' ) }}
                    </a>
                    <a class="text-white/80 no-underline transition hover:text-white" href="https://prosopo.io/?utm_source=procaptcha-wp&amp;utm_medium=plugin&amp;utm_campaign=settings&amp;utm_content=header" target="_blank">
                        {{ __( 'Website', 'prosopo-procaptcha' ) }}
                    </a>
                    <a class="px-3 py-1.5 rounded border border-solid border-white/40 text-white no-underline transition hover:bg-white/10"
                       href="https://portal.prosopo.io/?utm_source=procaptcha-wp&amp;utm_medium=plugin&amp;utm_campaign=settings&amp;utm_content=header"
                       target="_blank">
                        {{ __( 'Prosopo portal', 'prosopo-procaptcha' ) }}
                    </a>
                </nav>
            </div>

            <ul class="flex leading-none mt-0 mb-5 -mx-5 bg-white border-0 border-b border-solid border-gray-light">
                @foreach ($tabs as $tab)
                    <li class="m-0">
                        <a href="{{ string($tab, 'url') }}"
                                @class([
                                    'block font-medium tracking-wide py-5 px-8 no-underline border-0 border-b-2 border-solid transition hover:text-blue-dark focus:shadow-none',
                                    'text-navy border-pink' => bool($tab, 'is_active'),
                                    'text-blue border-transparent'=> false === bool($tab, 'is_active'),
                                ])>
                            {{ string($tab, 'title') }}
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($is_just_saved)
                <div class="mt-7 text-success-dark font-medium">
                    {{ __( 'Settings successfully saved.', 'prosopo-procaptcha' ) }}
                </div>
            @endif

            <div class="mt-7 flex gap-12">
                {!! $tab_content !!}
            </div>
        </div>
    </template>
</prosopo-procaptha-wp-settings>