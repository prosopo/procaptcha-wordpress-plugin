@use('Io\Prosopo\Procaptcha\Settings\Settings_Page')
@use('function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string')
@use('function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\arr')
@use('function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool')

<form action="" method="post" class="flex flex-col gap-5 max-w-sm w-full" autocomplete="off">
    <input type="hidden" name="_wpnonce" value="{{ $nonce }}">
    <input type="hidden" name="{{ Settings_Page::TAB_NAME }}" value="{{ $tab_name }}">

    @if ('' !== $inputs_title)
        <div class="flex items-center justify-between gap-5">
            <p>{{ $inputs_title }}</p>
        </div>
    @endif

    @foreach ($inputs as $input)
        <div class="flex items-center justify-between gap-5">
            <p class="text-sm min-w-0">{{ string($input, 'label') }}</p>

            @switch(string($input, 'type'))
                @case('text')
                @case('password')
                    <input name="{{ string($input, 'name') }}" type="{{ string($input, 'type') }}"
                           @if (! bool($input, 'is_optional')) required @endif
                           class="bg-white w-64 shrink-0 py-1.5 px-3 border border-solid border-gray rounded transition shadow-none outline-none
                           focus:border-blue focus:ring-2 focus:ring-blue/20"
                           placeholder="{{ string($input, 'placeholder') }}" value="{{ string($input, 'value') }}">
                    @break
                @case('color')
                    <div class="flex items-center gap-2 w-64 shrink-0" data-color-field>
                        <label class="relative w-9 h-9 shrink-0 rounded border border-solid border-gray overflow-hidden cursor-pointer
                        bg-[repeating-linear-gradient(45deg,#fff_0_4px,#e5e7eb_4px_8px)]">
                            <span class="absolute inset-0" data-color-swatch
                                  style="background-color: {{ string($input, 'value') }}"></span>
                            <input type="color" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   aria-label="{{ string($input, 'label') }}" data-color-picker>
                        </label>
                        <input name="{{ string($input, 'name') }}" type="text"
                               class="bg-white flex-1 min-w-0 py-1.5 px-3 border border-solid border-gray rounded transition shadow-none outline-none font-mono
                               focus:border-blue focus:ring-2 focus:ring-blue/20"
                               placeholder="{{ string($input, 'placeholder') }}" value="{{ string($input, 'value') }}"
                               data-color-value>
                        <button type="button" class="bg-transparent border-0 p-0 text-xs text-gray cursor-pointer underline hover:text-blue"
                                data-color-clear>{{ __('Default', 'prosopo-procaptcha') }}</button>
                    </div>
                    @break
                @case('select')
                    <select name="{{ string($input,'name') }}"
                            class="bg-white w-64 shrink-0 py-1.5 px-3 border border-solid border-gray rounded shadow-none outline-none
                            focus:border-blue">
                        @foreach(arr($input, 'options') as $value => $label)
                            <option value="{{ $value }}" @selected($value === string($input,'value'))>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @break
            @endswitch
        </div>

        @if ('' !== string($input, 'description'))
            <p class="-mt-3 mb-0 text-xs text-gray">{{ string($input, 'description') }}</p>
        @endif
    @endforeach

    @if ('' !== $checkboxes_title)
        <div class="my-3 flex items-center justify-between gap-5">
            <p class="font-bold text-sm">{{ $checkboxes_title }}</p>
        </div>
    @endif

    @foreach ($checkboxes as $checkbox)
        <label class="flex items-center justify-between gap-5">
            <span class="text-sm">{{ string($checkbox, 'label') }}</span>
            <div class="relative inline-block w-14 h-8 cursor-pointer">
                <input name="{{ string($checkbox, 'name') }}"
                       type="checkbox"
                       class="peer opacity-0 w-0 h-0"
                        @checked(bool($checkbox, 'value'))>
                <div class="absolute inset-0 bg-[#ccc] transition-all duration-500 rounded-3xl peer-checked:bg-pink"></div>
                <div class="absolute h-5 w-5 top-1/2 left-1/2 -translate-x-full -translate-y-1/2 bg-white transition-all transition-500 rounded-full peer-checked:translate-x-0"></div>
            </div>
        </label>
    @endforeach

    <br>

    <input class="py-2 px-4 bg-pink text-white font-semibold rounded transition cursor-pointer border-0
    hover:bg-pink-dark" type="submit"
           name="prosopo-captcha__submit"
           value="{{ __('Save','prosopo-procaptcha') }}">

</form>