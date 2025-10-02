<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'News Aggregator') }}</title>
    <link rel="icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-indigo-50 to-white text-gray-900">
    <header class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
            <a href="{{ route('articles.index') }}" class="text-2xl font-semibold flex items-center gap-2">
                <span>🗞️</span>
                <span>{{ config('app.name', 'News Aggregator') }}</span>
            </a>
            <form method="POST" action="{{ route('articles.fetch') }}" class="inline" onsubmit="handleFetchSubmit(this)">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white/10 border border-white/20 backdrop-blur rounded hover:bg-white/20 transition duration-200 flex items-center gap-2" title="Get the latest news from all sources">
                    <span class="fetch-text">🔄 Latest News</span>
                    <span class="fetch-loading hidden">⏳ Fetching...</span>
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if (session('status'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-800 border border-green-200 shadow-sm flex items-center gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    {{ session('status') }}
                </div>
            </div>
        @endif
        {{ $slot ?? '' }}
        @yield('content')
    </main>
    <footer class="py-8 border-t bg-white/70 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <span class="text-gray-700">© {{ date('Y') }} {{ config('app.name', 'News Aggregator') }}</span>
                <span class="hidden sm:inline">•</span>
                <span>Designed by Aqib</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('articles.index') }}" class="hover:text-indigo-700">Home</a>
                <a href="https://github.com/AqibManzoor/News-Aggregator" target="_blank" class="hover:text-indigo-700">GitHub</a>
            </div>
        </div>
    </footer>
    <button id="backToFiltersBtn" onclick="scrollToFilters()" class="hidden fixed bottom-20 right-6 z-40 px-4 py-2 rounded-full shadow bg-indigo-600 text-white text-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Back to Filters
    </button>
    <div id="toast" class="fixed top-20 right-6 z-50 hidden">
        <div class="px-4 py-3 rounded-md shadow bg-indigo-600 text-white text-sm">Fetching latest articles...</div>
    </div>
    @stack('scripts')
    @stack('styles')
</body>
<style>

.prose p{margin-bottom:0.5rem}
</style>
<script>
function handleFetchSubmit(form) {
  const button = form.querySelector('button[type="submit"]');
  const textSpan = button.querySelector('.fetch-text');
  const loadingSpan = button.querySelector('.fetch-loading');
  
  // Show loading state
  textSpan.classList.add('hidden');
  loadingSpan.classList.remove('hidden');
  button.disabled = true;
  button.classList.add('opacity-75', 'cursor-not-allowed');
  
  return true;
}
function scrollToFilters(){
  if (typeof window.scrollToFilters === 'function') { window.scrollToFilters(); return; }
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
</script>
</html>
