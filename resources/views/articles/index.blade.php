@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
  <aside class="lg:col-span-1">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6 max-h-[calc(100vh-2rem)] overflow-y-auto">
      <!-- Header -->
      <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">Filters</h2>
          <a href="{{ route('articles.index', ['clear_filters' => 1]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium" title="Clear all filters">Clear All</a>
        </div>
      </div>

      <form method="POST" action="{{ route('articles.filter') }}" class="p-4 space-y-6">
        @csrf
        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search Keywords</label>
          <input type="text" name="q" value="{{ old('q', $filters['q'] ?? '') }}" placeholder="Search articles..." 
                 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <!-- Date Range -->
        <div class="border-t border-gray-200 pt-4">
          <button type="button" class="flex items-center justify-between w-full text-sm font-medium text-gray-900 mb-3 hover:text-indigo-600" onclick="toggleSection('dateRange')">
            <span>Date Range</span>
            <svg class="w-4 h-4 transform transition-transform duration-200" id="dateRangeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div id="dateRange" class="space-y-3" style="display: none;">
            <div>
              <label class="block text-xs text-gray-600 mb-1">From</label>
              <input type="date" name="from" value="{{ old('from', $filters['from'] ?? '') }}" 
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
              <label class="block text-xs text-gray-600 mb-1">To</label>
              <input type="date" name="to" value="{{ old('to', $filters['to'] ?? '') }}" 
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
          </div>
        </div>


        <!-- Sources Filter -->
        <div class="border-t border-gray-200 pt-4">
          <button type="button" class="flex items-center justify-between w-full text-sm font-medium text-gray-900 mb-3 hover:text-indigo-600" onclick="toggleSection('sources')">
            <div class="flex items-center gap-2">
              <span>Sources</span>
              <span class="text-xs text-gray-500">{{ count($filters['sources'] ?? []) }} selected</span>
            </div>
            <svg class="w-4 h-4 transform transition-transform duration-200" id="sourcesIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div id="sources" class="space-y-3" style="display: none;">
            <div>
              <input type="text" id="sourceSearch" placeholder="Search sources..." 
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md" id="sourcesList">
              @foreach($sourcesList as $source)
                <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 source-item" data-name="{{ strtolower($source->name) }}">
                  <input type="checkbox" name="sources[]" value="{{ $source->slug }}" 
                         @checked(in_array($source->slug, old('sources', $filters['sources'] ?? [])))
                         class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                  <span class="ml-3 text-sm text-gray-700">{{ $source->name }}</span>
                </label>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Categories Filter -->
        <div class="border-t border-gray-200 pt-4">
          <button type="button" class="flex items-center justify-between w-full text-sm font-medium text-gray-900 mb-3 hover:text-indigo-600" onclick="toggleSection('categories')">
            <div class="flex items-center gap-2">
              <span>Categories</span>
              <span class="text-xs text-gray-500">{{ count($filters['categories'] ?? []) }} selected</span>
            </div>
            <svg class="w-4 h-4 transform transition-transform duration-200" id="categoriesIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div id="categories" class="space-y-3" style="display: none;">
            <div>
              <input type="text" id="categorySearch" placeholder="Search categories..." 
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md" id="categoriesList">
              @foreach($categoriesList as $category)
                <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 category-item" data-name="{{ strtolower($category->name) }}">
                  <input type="checkbox" name="categories[]" value="{{ $category->slug }}" 
                         @checked(in_array($category->slug, old('categories', $filters['categories'] ?? [])))
                         class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                  <span class="ml-3 text-sm text-gray-700">{{ $category->name }}</span>
                </label>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Authors Filter -->
        <div class="border-t border-gray-200 pt-4">
          <button type="button" class="flex items-center justify-between w-full text-sm font-medium text-gray-900 mb-3 hover:text-indigo-600" onclick="toggleSection('authors')">
            <div class="flex items-center gap-2">
              <span>Authors</span>
              <span class="text-xs text-gray-500">{{ count($filters['authors'] ?? []) }} selected</span>
            </div>
            <svg class="w-4 h-4 transform transition-transform duration-200" id="authorsIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div id="authors" class="space-y-3" style="display: none;">
            <div>
              <input type="text" id="authorSearch" placeholder="Search authors..." 
                     class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md" id="authorsList">
              @foreach($authorsList as $author)
                <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 author-item" data-name="{{ strtolower($author->name) }}">
                  <input type="checkbox" name="authors[]" value="{{ $author->name }}" 
                         @checked(in_array($author->name, old('authors', $filters['authors'] ?? [])))
                         class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                  <span class="ml-3 text-sm text-gray-700">{{ $author->name }}</span>
                </label>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Sort Options -->
        <div class="border-t border-gray-200 pt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
          <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="" @selected(old('sort', $filters['sort'] ?? '') === '')>Newest First</option>
            <option value="oldest" @selected(old('sort', $filters['sort'] ?? '') === 'oldest')>Oldest First</option>
            <option value="title" @selected(old('sort', $filters['sort'] ?? '') === 'title')>Title A-Z</option>
          </select>
        </div>

        <!-- Action Buttons -->
        <div class="border-t border-gray-200 pt-4">
          <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
              Apply Filters
            </button>
            <a href="{{ route('articles.index', ['clear_filters' => 1]) }}" class="px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
              Reset
            </a>
          </div>
        </div>
      </form>
    </div>
  </aside>

  <section class="lg:col-span-3 space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Articles</h1>
          <p class="text-sm text-gray-600 mt-1">{{ number_format($articles->total()) }} articles found</p>
        </div>
        <div class="text-sm text-gray-500">
          @if(!empty(array_filter($filters ?? [])))
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
              Filtered
            </span>
          @endif
        </div>
      </div>
    </div>

    <!-- Articles List -->
    <div class="space-y-4">
      @forelse($articles as $article)
        <article class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
          <div class="p-6">
            <div class="flex gap-4">
              @if($article->image_url)
                <div class="flex-shrink-0">
                  <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-32 h-24 object-cover rounded-lg">
                </div>
              @endif
              <div class="flex-1 min-w-0">
                <a href="{{ $article->url }}" target="_blank" class="text-xl font-semibold text-gray-900 hover:text-indigo-600 transition-colors duration-200 line-clamp-2">
                  {{ $article->title }}
                </a>
                
                <!-- Meta Information -->
                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                  <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    {{ $article->source->name ?? 'Unknown Source' }}
                  </span>
                  @if($article->published_at)
                    <span class="inline-flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      {{ $article->published_at->format('M d, Y') }}
                    </span>
                  @endif
                  @if($article->language)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ strtoupper($article->language) }}
                    </span>
                  @endif
                </div>

                <!-- Summary -->
                @if($article->summary)
                  <p class="mt-3 text-gray-700 line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($article->summary), 200) }}</p>
                @endif

                <!-- Tags -->
                <div class="mt-4 flex flex-wrap gap-2">
                  @foreach($article->categories as $category)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                      #{{ $category->name }}
                    </span>
                  @endforeach
                  @foreach($article->authors as $author)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      @ {{ $author->name }}
                    </span>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </article>
      @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No articles found</h3>
          <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search terms.</p>
          <div class="mt-6">
            <a href="{{ route('articles.index', ['clear_filters' => 1]) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Clear all filters
            </a>
          </div>
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    @if($articles->hasPages())
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        {{ $articles->links('pagination.custom') }}
      </div>
    @endif
  </section>
</div>

@push('scripts')
<script>
// Toggle collapsible sections
function toggleSection(sectionId) {
  const section = document.getElementById(sectionId);
  const icon = document.getElementById(sectionId + 'Icon');
  
  if (section && icon) {
    if (section.style.display === 'none' || section.style.display === '') {
      section.style.display = 'block';
      icon.style.transform = 'rotate(180deg)';
    } else {
      section.style.display = 'none';
      icon.style.transform = 'rotate(0deg)';
    }
  }
}

// Filter search functionality
function setupFilterSearch(searchInputId, listId, itemClass) {
  const searchInput = document.getElementById(searchInputId);
  const list = document.getElementById(listId);
  const items = list.querySelectorAll('.' + itemClass);
  
  if (searchInput && list) {
    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      
      items.forEach(item => {
        const name = item.getAttribute('data-name');
        if (name.includes(searchTerm)) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    });
  }
}

// Update filter counts in real-time
function updateFilterCounts() {
  // Update sources count
  const sourceCheckboxes = document.querySelectorAll('input[name="sources[]"]');
  const sourceCount = document.querySelector('#sources .text-xs.text-gray-500');
  if (sourceCount) {
    const checkedSources = Array.from(sourceCheckboxes).filter(cb => cb.checked).length;
    sourceCount.textContent = `${checkedSources} selected`;
  }
  
  // Update categories count
  const categoryCheckboxes = document.querySelectorAll('input[name="categories[]"]');
  const categoryCount = document.querySelector('#categories .text-xs.text-gray-500');
  if (categoryCount) {
    const checkedCategories = Array.from(categoryCheckboxes).filter(cb => cb.checked).length;
    categoryCount.textContent = `${checkedCategories} selected`;
  }
  
  // Update authors count
  const authorCheckboxes = document.querySelectorAll('input[name="authors[]"]');
  const authorCount = document.querySelector('#authors .text-xs.text-gray-500');
  if (authorCount) {
    const checkedAuthors = Array.from(authorCheckboxes).filter(cb => cb.checked).length;
    authorCount.textContent = `${checkedAuthors} selected`;
  }
}


document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  if (form) {
    form.addEventListener('submit', function() {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Applying...';
      }
    });
  }
  
  // Setup filter search functionality
  setupFilterSearch('sourceSearch', 'sourcesList', 'source-item');
  setupFilterSearch('categorySearch', 'categoriesList', 'category-item');
  setupFilterSearch('authorSearch', 'authorsList', 'author-item');
  
  // Add event listeners for real-time count updates
  document.addEventListener('change', function(e) {
    if (e.target.matches('input[name="sources[]"], input[name="categories[]"], input[name="authors[]"]')) {
      updateFilterCounts();
    }
  });
  
  // Auto-expand sections with active filters
  const activeFilters = {
    dateRange: {{ (!empty($filters['from']) || !empty($filters['to'])) ? 'true' : 'false' }},
    sources: {{ count($filters['sources'] ?? []) > 0 ? 'true' : 'false' }},
    categories: {{ count($filters['categories'] ?? []) > 0 ? 'true' : 'false' }},
    authors: {{ count($filters['authors'] ?? []) > 0 ? 'true' : 'false' }}
  };
  
  Object.keys(activeFilters).forEach(sectionId => {
    if (activeFilters[sectionId]) {
      toggleSection(sectionId);
    }
  });
  
  // Add keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
      const form = document.querySelector('form');
      if (form) {
        form.submit();
      }
    }
    
    // Escape to clear search inputs
    if (e.key === 'Escape') {
      const searchInputs = document.querySelectorAll('input[id$="Search"]');
      searchInputs.forEach(input => {
        input.value = '';
        input.dispatchEvent(new Event('input'));
      });
    }
  });
});
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom scrollbar for filter sections */
.max-h-48::-webkit-scrollbar {
  width: 6px;
}

.max-h-48::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.max-h-48::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.max-h-48::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
@endpush
@endsection
