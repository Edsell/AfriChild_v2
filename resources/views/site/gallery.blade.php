@include('site.headers.head', ['title' => 'Gallery'])

@include('site.headers.pg-crumb', [
  'title' => 'Gallery',
  'items' => [
    ['label' => 'Home', 'url' => route('site.home')],
    ['label' => 'Gallery']
  ]
])

@php
  $activeSlug = $activeSlug ?? null;
@endphp

<style>
  .af-filter-wrap{ padding: 28px 0 18px; text-align:center; }
  .af-filter{ display:inline-flex; flex-wrap:wrap; gap:12px; justify-content:center; align-items:center; }
  .af-filter .btn-filter{
    border:0; border-radius:6px; padding:10px 18px; font-weight:600; line-height:1;
    background:#111; color:#fff; box-shadow:0 6px 16px rgba(0,0,0,.12);
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    text-decoration:none; display:inline-flex; align-items:center; justify-content:center;
  }
  .af-filter .btn-filter:hover{ transform: translateY(-1px); box-shadow:0 10px 24px rgba(0,0,0,.14); }
  .af-filter .btn-filter.is-active{ background:#51C4C9; position:relative; }
  .af-filter .btn-filter.is-active::after{
    content:""; position:absolute; left:50%; transform:translateX(-50%); bottom:-8px;
    width:0;height:0;border-left:8px solid transparent;border-right:8px solid transparent;border-top:8px solid #51C4C9;
  }

  .af-gallery-section{ padding: 10px 0 65px; }
  .af-grid{
    display:grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap:0;
    overflow:hidden;
  }
  @media (max-width: 992px){ .af-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 576px){ .af-grid{ grid-template-columns: 1fr; } }

  .af-tile{ position:relative; min-height:300px; background:#f2f2f2; overflow:hidden; }
  .af-tile img{ width:100%; height:100%; object-fit:cover; display:block; transform:scale(1); transition:transform .25s ease; }
  .af-tile:hover img{ transform:scale(1.04); }

  .af-overlay{
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center; text-align:center;
    padding:22px; opacity:0; transition:opacity .2s ease; background:rgba(35,44,55,.78);
  }
  .af-tile:hover .af-overlay{ opacity:1; }

  .af-meta{ color:rgba(255,255,255,.85); font-weight:600; font-size:13px; letter-spacing:.2px; text-transform:uppercase; margin-bottom:6px; }
  .af-title{ color:#fff; font-weight:800; font-size:26px; line-height:1.15; margin:0 0 10px; }
  .af-desc{ color:rgba(255,255,255,.85); max-width:420px; margin:0 auto 18px; }

  .af-actions{ display:inline-flex; gap:10px; justify-content:center; align-items:center; }
  .af-action{
    width:44px; height:44px; border-radius:999px; background:#fff;
    display:inline-flex; align-items:center; justify-content:center;
    box-shadow:0 10px 22px rgba(0,0,0,.25);
    transition:transform .15s ease;
  }
  .af-action:hover{ transform:translateY(-1px); }
  .af-action svg{ width:18px; height:18px; }
</style>

<section class="af-gallery-section">
  <div class="container">

    {{-- Filters (server-side) --}}
    <div class="af-filter-wrap">
      <div class="af-filter">
        <a href="{{ route('site.gallery.index') }}"
           class="btn-filter {{ empty($activeSlug) ? 'is-active' : '' }}">
          Show All
        </a>

        @foreach($galleries as $g)
          <a href="{{ route('site.gallery.show', $g->slug) }}"
             class="btn-filter {{ ($activeSlug === $g->slug) ? 'is-active' : '' }}">
            {{ $g->name }}
          </a>
        @endforeach
      </div>
    </div>

    {{-- Grid --}}
    <div class="af-grid" id="afGalleryGrid">
      @forelse($items as $it)
        @php
          $gname = $it->gallery->name ?? 'Gallery';
          $title = $it->alt ?: ($it->gallery->name ?? 'Gallery Item');
          $desc  = 'Click to preview this photo.';

          // Robust image URL:
          // - if already absolute URL => use as-is
          // - else assume stored on public disk => Storage::url()
          // - fallback => asset()
          $raw = $it->image_url ?: ($it->image ?? '');
          if ($raw && preg_match('~^https?://~i', $raw)) {
            $img = $raw;
          } else {
            $path = ltrim($raw, '/');
            $img  = $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : '';
            if (!$path) $img = '';
          }
        @endphp

        <div class="af-tile">
          @if($img)
            <img src="{{ $img }}" alt="{{ $it->alt ?? '' }}">
          @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
              <span class="text-muted">Image missing</span>
            </div>
          @endif

          <div class="af-overlay">
            <div>
              <div class="af-meta">{{ $gname }}</div>
              <h3 class="af-title">{{ $title }}</h3>
              <p class="af-desc">{{ $desc }}</p>

              @if($img)
                <div class="af-actions">
                  <a href="{{ $img }}"
                     class="af-action glightbox"
                     data-gallery="africhild-gallery"
                     aria-label="Preview image">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                  </a>

                  <a href="{{ $img }}"
                     class="af-action"
                     target="_blank"
                     rel="noopener"
                     aria-label="Open image">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path>
                      <path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path>
                    </svg>
                  </a>
                </div>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="alert alert-light border mt-3">
          No images found yet.
        </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    @if(method_exists($items, 'links'))
      <div class="mt-4 d-flex justify-content-center">
        {{ $items->onEachSide(1)->links('site.footers.pagination.gallery_page') }}
      </div>
    @endif

  </div>
</section>

{{-- Lightbox --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
  GLightbox({ selector: '.glightbox' });
</script>

@include('site.footers.foot')