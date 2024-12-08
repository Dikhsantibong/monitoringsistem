<nav class="flex px-1 py-3 text-gray-700 mb-2" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        @foreach($breadcrumbs ?? [] as $breadcrumb)
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    @if(isset($breadcrumb['url']))
                        <a href="{{ $breadcrumb['url'] }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                            {{ $breadcrumb['title'] }}
                        </a>
                    @else
                        <span class="text-sm font-medium text-gray-500">{{ $breadcrumb['title'] }}</span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav> 