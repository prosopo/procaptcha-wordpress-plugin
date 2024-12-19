@use('Io\Prosopo\Procaptcha\Settings\Settings_Page')

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
            <p class="text-sm">{{ $input->get_string('label') }}</p>

            @switch($input->get_string('type'))
                @case('text')
                @case('password')
                    <input name="{{ $input->get_string('name') }}" type="{{ $input->get_string('type') }}" required
                           class="bg-white w-64 py-1.5 px-3 border border-gray rounded transition shadow-none outline-none
                           focus:border-blue"
                           placeholder="{{ $input->get_string('label') }}" value="{{ $input->get_string('value') }}">
                    @break
                @case('select')
                    <select name="{{ $input->get_string('name') }}"
                            class="bg-white w-64 py-1.5 px-3 border border-gray rounded shadow-none outline-none
                            focus:border-blue">
                        @foreach($input->get_array('options') as $value => $label)
                            <option value="{{ $value }}" @selected($value === $input->get_string('value'))>
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
            <span class="text-sm">{{ $checkbox->get_string('label') }}</span>
            <div class="relative inline-block w-14 h-8">
                <input name="{{ $checkbox->get_string('name') }}"
                       type="checkbox"
                       class="peer opacity-0 w-0 h-0"
                        @checked($checkbox->get_bool('value'))>
                <div class="absolute cursor-pointer inset-0 bg-[#ccc] transition-all duration-500 rounded-3xl peer-checked:bg-[#2196F3]
                before:absolute before:h-5 before:w-5 before:left-1.5 before:bottom-1.5 before:bg-white before:transition-all before:transition-500 before:rounded-full peer-checked:before:translate-x-6">

                </div>
            </div>
        </label>
    @endforeach

    <br>

    <input class="py-1.5 px-3 bg-blue text-white rounded transition cursor-pointer
    hover:bg-blue-dark" type="submit"
           name="prosopo-captcha__submit"
           value="{{ __('Save','prosopo-procaptcha') }}">

</form>