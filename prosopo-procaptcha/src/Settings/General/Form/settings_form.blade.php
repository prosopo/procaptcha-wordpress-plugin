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
            <p class="text-sm">{{ string($input, 'label') }}</p>

            @switch(string($input, 'type'))
                @case('text')
                @case('password')
                    <input name="{{ string($input, 'name') }}" type="{{ string($input, 'type') }}" required
                           class="bg-white w-64 py-1.5 px-3 border border-solid border-gray rounded transition shadow-none outline-none
                           focus:border-blue"
                           placeholder="{{ string($input, 'label') }}" value="{{ string($input, 'value') }}">
                    @break
                @case('select')
                    <select name="{{ string($input,'name') }}"
                            class="bg-white w-64 py-1.5 px-3 border border-solid border-gray rounded shadow-none outline-none
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
                <div class="absolute inset-0 bg-[#ccc] transition-all duration-500 rounded-3xl peer-checked:bg-[#2196F3]"></div>
                <div class="absolute h-5 w-5 top-1/2 left-1/2 -translate-x-full -translate-y-1/2 bg-white transition-all transition-500 rounded-full peer-checked:translate-x-0"></div>
            </div>
        </label>
    @endforeach

    <br>

    <input class="py-1.5 px-3 bg-blue text-white rounded transition cursor-pointer
    hover:bg-blue-dark" type="submit"
           name="prosopo-captcha__submit"
           value="{{ __('Save','prosopo-procaptcha') }}">

</form>