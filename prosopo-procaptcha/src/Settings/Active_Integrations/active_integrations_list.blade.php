<div>
    @foreach($active_integrations as $active_integration)
        <div>
            <a href="{{$active_integration->docs_url}}" target="_blank">
                {{ $active_integration->name }}
            </a>
        </div>
    @endforeach
</div>