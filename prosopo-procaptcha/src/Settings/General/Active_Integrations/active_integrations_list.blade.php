<div class="flex flex-col bg-white rounded w-full p-4">

    <div class="flex items-center gap-2">
        <span class="text-yellow icon-[material-symbols--activity-zone] w-6 h-6"></span>
        <h2 class="font-medium text-base">{{ $label }}</h2>
    </div>

    <div class="mt-6 max-w-140">{{ $description }}</div>

    <div class="my-8 flex flex-col gap-2">
        @foreach($active_integrations as $active_integration)
            <div class="flex items-center gap-2">
                <span class="text-green-700 icon-[material-symbols--check-box] w-6 h-6"></span>
                <a class="text-sm font-medium text-blue hover:text-black transition"
                   href="{{$active_integration->docs_url}}" target="_blank">
                    {{ $active_integration->name }}
                </a>
            </div>
        @endforeach
    </div>

    <div class="flex items-center gap-2">
        <span class="text-blue icon-[material-symbols--info-rounded] w-5 h-5"></span>
        <div>{{ $details }}</div>
    </div>

    <div class="mt-6 border-solid border-t-1 border-gray pt-2">
        <div class="max-w-140 text-[13px]">
            {!! $request_new !!}
        </div>
    </div>

</div>