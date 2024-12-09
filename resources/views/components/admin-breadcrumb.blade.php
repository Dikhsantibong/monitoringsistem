
<nav class="px-6 pb-4">
    <ol class="flex text-sm">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                Dashboard
            </a>
        </li>
        @foreach($breadcrumbs as $breadcrumb)
            <li class="mx-2 text-gray-500">/</li>
            <li class="{{ !$breadcrumb['url'] ? 'text-gray-700' : 'text-blue-600 hover:text-blue-800' }}">
                @if($breadcrumb['url'])
                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                @else
                    {{ $breadcrumb['name'] }}
                @endif
            </li>
        @endforeach
    </ol>
</nav> 