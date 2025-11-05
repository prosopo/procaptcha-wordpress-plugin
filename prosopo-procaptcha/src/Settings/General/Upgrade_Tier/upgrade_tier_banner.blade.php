<div class="flex flex-col items-start gap-4">
    <div class="flex gap-2 items-center">
        <p class="text-lg">{{ $title }}</p>
        <span class="icon-[material-symbols--family-star] w-7 h-7 bg-yellow-500"></span>
    </div>

    <ul class="flex flex-col gap-1">
        @foreach($benefits as $benefit)
            <li class="flex items-center gap-2">
                <div class="w-2 h-2 bg-indigo-800 shrink-0"></div>
                <p>{{ $benefit }}</p>
            </li>
        @endforeach
    </ul>

    <a target="_blank"
       rel="noreferrer"
       href="{{ $button_url }}"
       class="flex items-center gap-2 px-4 py-2 text-sm rounded text-white bg-indigo-800 transition cursor-pointer
    hover:bg-indigo-700">
        <span>{{ $button_label }}</span>
        <span class="icon-[material-symbols--upgrade] w-6 h-6"></span>
    </a>
</div>