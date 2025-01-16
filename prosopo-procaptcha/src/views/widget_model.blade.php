@use('Io\Prosopo\Procaptcha\Widget\Procaptcha_Widget')

@php
    $hidden_input_defaults = [
		'type' => 'hidden',
		'name' => Procaptcha_Widget::FORM_FIELD_NAME,
		'class' => 'prosopo-procaptcha-input'
    ];
@endphp

@if (! $is_stub)
    <div {!! $attributes->merge(['class' => 'prosopo-procaptcha-wp__wrapper',]) !!}>
        <prosopo-procaptcha-wp-widget class="prosopo-procaptcha-wp-widget" style="display: block;">
            <div class="prosopo-procaptcha"></div>
        </prosopo-procaptcha-wp-widget>

        @if(! $no_client_validation)
            <prosopo-procaptcha-wp-form class="prosopo-procaptcha-wp-form" style="display: block;line-height: 1;">
	            <span class="prosopo-procaptcha-wp-form__error"
                      style="display:block;visibility: {{ $get_error_visibility() }};color:red;line-height:1;font-size: 12px;padding:3px 0 0 10px;">
		            {{ $error_message }}
	            </span>
            </prosopo-procaptcha-wp-form>
        @endif

        @if (! $hidden_input_attrs->empty())
            <input {!! $hidden_input_attrs->merge($hidden_input_defaults, true) !!}>
        @endif

    </div>
@else
    <input {!! $hidden_input_attrs->merge($hidden_input_defaults, true)
->merge(['value'=> 'authorized_user',], true) !!}>
@endif
