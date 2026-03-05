{{-- resources/views/site/team/type.blade.php --}}
@php
  use App\Models\TeamMember;
  use Illuminate\Support\Facades\Storage;

  $pageTitle = $typeLabel;
  $crumbTitle = $typeLabel;

  $crumbItems = [
    ['label' => 'About', 'url' => route('site.about')],
    ['label' => 'Team', 'url' => route('site.team.index')],
  ];

  $typeOptions = TeamMember::typeOptions();

  $isPeople = in_array($typeKey, [
    TeamMember::TYPE_BOARD,
    TeamMember::TYPE_SECRETARIAT,
  ], true);

  $group = collect($teamMembers)
      ->where('is_active', true)
      ->sortBy('sort_order')
      ->values();

  $resolveImg = function ($path) {
    if (!$path) return null;

    $path = trim($path);
    if (preg_match('~^https?://~i', $path)) return $path;

    $clean = ltrim($path, '/');

    if (str_starts_with($clean, 'storage/')) {
      return asset($clean);
    }

    try {
      return Storage::disk('public')->url($clean);
    } catch (\Throwable $e) {
      return asset($clean);
    }
  };
@endphp

@include('site.headers.head', ['title' => $pageTitle])

@include('site.headers.pg-crumb', [
  'title' => $crumbTitle,
  'items' => $crumbItems,
])

<section id="about-team" class="vc_row wpb_row vc_row-fluid vc-zozo-section typo-default" style="padding: 55px 0; background:#fafafa;">
  <div class="zozo-vc-main-row-inner vc-normal-section">
    <div class="container">

      <div class="zozo-parallax-header">
        <div class="parallax-header content-style-default">
          <h2 class="parallax-title">Meet The People</h2>
          <div class="parallax-desc default-style" style="max-width: 820px;">
            Meet the people and organizations supporting AfriChild’s mission.
          </div>
        </div>
      </div>

      <div class="ac-team-group" style="margin-top: 34px;">

        <div class="d-flex align-items-center justify-content-between" style="gap:12px; margin-bottom: 14px; flex-wrap:wrap;">
          <h3 style="margin:0; font-size: 22px; font-weight: 800;">
            {{ $typeLabel }}
          </h3>
        </div>

        @if($group->count() === 0)
          <div style="background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:14px; padding:16px 14px; box-shadow: 0 6px 18px rgba(0,0,0,.06);">
            No records found for {{ $typeLabel }}.
          </div>
        @else

          @if($isPeople)
            {{-- PEOPLE GRID (equal-height cards: fixes long names/designations spacing) --}}
            <div class="row g-4 ac-people-grid">
              @foreach($group as $m)
                @php
                  $photoUrl = $resolveImg($m->photo ?? null);
                  $hasAnySocial = filled($m->facebook) || filled($m->twitter) || filled($m->linkedin) || filled($m->instagram);
                @endphp

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                  <article class="ac-team-card w-100">
                    <div class="ac-team-photo">
                      @if($photoUrl)
                        <img loading="lazy" decoding="async" src="{{ $photoUrl }}" alt="{{ $m->name }}">
                      @else
                        <div class="ac-photo-fallback">
                          <div class="ac-fallback-initial">
                            {{ strtoupper(mb_substr(trim($m->name), 0, 1)) }}
                          </div>
                        </div>
                      @endif
                    </div>

                    <div class="ac-team-body">
                      <div class="ac-team-name" title="{{ $m->name }}">{{ $m->name }}</div>

                      @if($m->designation)
                        <div class="ac-team-role" title="{{ $m->designation }}">{{ $m->designation }}</div>
                      @else
                        <div class="ac-team-role ac-empty-role" aria-hidden="true">&nbsp;</div>
                      @endif

                      <div class="ac-team-social {{ $hasAnySocial ? '' : 'is-empty' }}">
                        @if($m->facebook)<a href="{{ $m->facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa fa-facebook"></i></a>@endif
                        @if($m->twitter)<a href="{{ $m->twitter }}" target="_blank" rel="noopener" aria-label="X/Twitter"><i class="fa-brands fa-x-twitter"></i></a>@endif
                        @if($m->linkedin)<a href="{{ $m->linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a>@endif
                        @if($m->instagram)<a href="{{ $m->instagram }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa fa-instagram"></i></a>@endif
                      </div>
                    </div>
                  </article>
                </div>
              @endforeach
            </div>

          @else
            {{-- PARTNERS GRID --}}
            <div class="row g-4 ac-partner-grid">
              @foreach($group as $m)
                @php
                  $logoUrl = $resolveImg($m->photo ?? null);
                  $link = $m->linkedin ?: ($m->facebook ?: ($m->twitter ?: ($m->instagram ?: null)));
                @endphp

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                  <div class="ac-partner-card w-100">
                    @if($link)
                      <a href="{{ $link }}" target="_blank" rel="noopener" class="ac-partner-link" aria-label="{{ $m->name }}">
                    @endif

                    <div class="ac-partner-logo">
                      @if($logoUrl)
                        <img loading="lazy" decoding="async" src="{{ $logoUrl }}" alt="{{ $m->name }}">
                      @else
                        <div class="ac-partner-fallback">{{ $m->name }}</div>
                      @endif
                    </div>

                    <div class="ac-partner-name">{{ $m->name }}</div>

                    @if($link)
                      </a>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif

        @endif
      </div>

    </div>
  </div>

  <style>
    /* ---------- Shared card shell ---------- */
    #about-team .ac-team-card,
    #about-team .ac-partner-card{
      height:100%;
      display:flex;
      flex-direction:column;
      background:#fff;
      border:1px solid rgba(0,0,0,.06);
      border-radius:14px;
      overflow:hidden;
      box-shadow:0 6px 18px rgba(0,0,0,.06);
    }

    /* ---------- Team photo ---------- */
    #about-team .ac-team-photo{
      height:260px;
      background:#f2f2f2;
      overflow:hidden;
      display:flex;
      align-items:center;
      justify-content:center;
      flex:0 0 auto;
    }

    #about-team .ac-team-photo img{
      width:100%;
      height:100%;
      object-fit:cover;
      object-position:center;
      display:block;
    }

    #about-team .ac-photo-fallback{
      width:100%;
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      background:linear-gradient(180deg,#f5f5f5,#ececec);
    }

    #about-team .ac-fallback-initial{
      width:84px;
      height:84px;
      border-radius:999px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:32px;
      font-weight:900;
      background:rgba(0,0,0,.07);
      color:rgba(0,0,0,.55);
    }

    /* ---------- Team body: FIXED RHYTHM (solves spacing) ---------- */
    #about-team .ac-team-body{
      padding:14px 12px 16px;
      text-align:center;
      background:#eee;
      display:flex;
      flex-direction:column;
      flex:1 1 auto;
      min-height:170px;
    }

    /* Long names were the real spacing breaker */
    #about-team .ac-team-name{
      font-weight:800;
      margin-bottom:6px;
      line-height:1.25;
      color:#243746;
      overflow-wrap:anywhere;
      word-break:break-word;

      display:-webkit-box;
      -webkit-box-orient:vertical;
      -webkit-line-clamp:2;
      overflow:hidden;
      min-height:calc(1.25em * 2);
    }

    #about-team .ac-team-role{
      opacity:.82;
      margin-bottom:12px;
      line-height:1.35;
      color:#5f6f7c;
      overflow-wrap:anywhere;
      word-break:break-word;

      display:-webkit-box;
      -webkit-box-orient:vertical;
      -webkit-line-clamp:2;
      overflow:hidden;
      min-height:calc(1.35em * 2);
    }

    #about-team .ac-team-role.ac-empty-role{
      visibility:hidden;
    }

    #about-team .ac-team-social{
      display:flex;
      justify-content:center;
      gap:10px;
      flex-wrap:wrap;
      margin-top:auto;
      min-height:40px; /* reserve row so no card shifts */
      align-items:center;
    }

    #about-team .ac-team-social.is-empty{
      visibility:hidden;
    }

    #about-team .ac-team-social a{
      width:40px;
      height:40px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid rgba(0,0,0,.12);
      background:rgba(255,255,255,.3);
      text-decoration:none;
      border-radius:10px;
    }

    /* ---------- Partners ---------- */
    #about-team .ac-partner-card{
      padding:16px 14px;
      text-align:center;
    }

    #about-team .ac-partner-link{
      color:inherit;
      text-decoration:none;
      display:flex;
      flex-direction:column;
      height:100%;
    }

    #about-team .ac-partner-logo{
      height:90px;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#f7f7f7;
      border-radius:12px;
      overflow:hidden;
      margin-bottom:10px;
      padding:8px;
      flex:0 0 auto;
    }

    #about-team .ac-partner-logo img{
      max-height:74px;
      max-width:100%;
      object-fit:contain;
      display:block;
    }

    #about-team .ac-partner-fallback{
      font-weight:800;
      padding:8px;
      overflow-wrap:anywhere;
      word-break:break-word;
    }

    #about-team .ac-partner-name{
      font-weight:800;
      margin-top:6px;
      line-height:1.2;
      flex:1;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow-wrap:anywhere;
      word-break:break-word;
    }

    @media (max-width: 575.98px){
      #about-team .ac-team-photo{ height:240px; }
      #about-team .ac-team-body{ min-height:160px; padding:12px 10px 14px; }
    }
  </style>
</section>

@include('site.footers.foot')