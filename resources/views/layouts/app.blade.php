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
            <form method="POST" action="{{ route('articles.fetch') }}" class="inline" onsubmit="showFetchingToast()">
                @csrf
                <button class="px-3 py-2 bg-white/10 border border-white/20 backdrop-blur rounded hover:bg-white/20 transition" title="Fetch latest from providers">Latest Articles</button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
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
                <a href="https://github.com" target="_blank" class="hover:text-indigo-700">GitHub</a>
            </div>
        </div>
    </footer>
    <div id="toast" class="fixed top-20 right-6 z-50 hidden">
        <div class="px-4 py-3 rounded-md shadow bg-indigo-600 text-white text-sm">Fetching latest articles...</div>
    </div>
    @stack('scripts')
    @stack('styles')
</body>
<style>
/* Basic prose for article content */
.prose p{margin-bottom:0.5rem}
</style>
<script>
function showFetchingToast(){
  var t=document.getElementById('toast');
  if(!t) return true;
  t.classList.remove('hidden');
  setTimeout(function(){ t.classList.add('hidden'); }, 2500);
  return true;
}
</script>
</html>
