<div class="flex flex-col items-start gap-4">
    <div class="flex gap-2 items-center">
        <p class="m-0 text-lg font-semibold text-navy">{{ $title }}</p>
        <span class="icon-[material-symbols--workspace-premium-outline] w-6 h-6 bg-pink"></span>
    </div>

    <ul class="flex flex-col gap-1">
        @foreach($benefits as $benefit)
            <li class="flex items-center gap-2">
                <span class="icon-[material-symbols--check] w-4 h-4 bg-pink shrink-0"></span>
                <p class="m-0">{{ $benefit }}</p>
            </li>
        @endforeach
    </ul>

    <a target="_blank"
       rel="noreferrer"
       href="{{ $button_url }}"
       class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded text-white bg-pink no-underline transition cursor-pointer
    hover:bg-pink-dark">
        <span>{{ $button_label }}</span>
        <span class="icon-[material-symbols--upgrade] w-6 h-6"></span>
    </a>
</div>