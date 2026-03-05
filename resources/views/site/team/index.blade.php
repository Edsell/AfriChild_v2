@php
  use Illuminate\Support\Facades\Storage;

  $pageTitle = 'Team';
  $crumbTitle = 'Team';
  $crumbItems = [
    ['label' => 'Home', 'url' => route('site.home')],
    ['label' => 'About', 'url' => route('site.about')],
    ['label' => 'Team'],
  ];


  $resolveImg = function ($path) {
    if (!$path) return null;

    $path = trim((string) $path);

    // Absolute URL
    if (preg_match('~^https?://~i', $path)) {
      return $path;
    }

    // Normalize slashes
    $clean = ltrim($path, '/');

    // Already a public storage URL/path
    // e.g. "storage/team/john.jpg"
    if (str_starts_with($clean, 'storage/')) {
      return asset($clean);
    }

    // Common public web paths (theme/assets/uploads/etc.)
    // e.g. "assets/img/...", "uploads/team/..."
    if (
      str_starts_with($clean, 'assets/') ||
      str_starts_with($clean, 'uploads/') ||
      str_starts_with($clean, 'public/')
    ) {
      return asset($clean);
    }

    // If file physically exists under /public, serve as public asset
    if (file_exists(public_path($clean))) {
      return asset($clean);
    }

    // Otherwise assume it's a path stored on the public disk (DB value like "team/john.jpg")
    try {
      return Storage::disk('public')->url($clean);
    } catch (\Throwable $e) {
      // Final fallback
      return asset($clean);
    }
  };
@endphp

@include('site.headers.head', ['title' => $pageTitle])
@include('site.headers.pg-crumb', ['title' => $crumbTitle, 'items' => $crumbItems])

<section id="about-team" class="vc_row wpb_row vc_row-fluid vc-zozo-section typo-default" style="padding:55px 0; background:#fafafa;">
  <div class="zozo-vc-main-row-inner vc-normal-section">
    <div class="container">

      <div class="zozo-parallax-header">
        <div class="parallax-header content-style-default">
          <h2 class="parallax-title">Our Team &amp; Partners</h2>
          <div class="parallax-desc default-style" style="max-width:820px;">
            Meet the people and organizations supporting AfriChild’s mission.
          </div>
        </div>
      </div>

      @foreach($typeOrder as $typeKey)
        @php
          $group = ($teamByType->get($typeKey, collect()))->values();

          $isPeopleGroup = in_array($typeKey, [
            \App\Models\TeamMember::TYPE_BOARD,
            \App\Models\TeamMember::TYPE_SECRETARIAT,
          ], true);
        @endphp

        @if($group->count() > 0)
          <div class="ac-team-group" style="margin-top:34px;">

            <div class="d-flex align-items-center justify-content-between" style="gap:12px; margin-bottom:14px; flex-wrap:wrap;">
              <h3 style="margin:0; font-size:22px; font-weight:800;">
                {{ $typeOptions[$typeKey] ?? ucfirst(str_replace('_',' ', $typeKey)) }}
              </h3>

              @php
                $slug = match($typeKey) {
                  \App\Models\TeamMember::TYPE_BOARD => 'board',
                  \App\Models\TeamMember::TYPE_SECRETARIAT => 'secretariat',
                  \App\Models\TeamMember::TYPE_FOUNDING_MEMBERS => 'founding-members',
                  \App\Models\TeamMember::TYPE_PROMOTING_PARTNERS => 'promoting-partners',
                  default => null,
                };
              @endphp

              @if($slug)
                <a href="{{ route('site.team.type', ['type' => $slug]) }}"
                   class="vc_general vc_btn3 vc_btn3-size-sm vc_btn3-shape-rounded vc_btn3-style-default vc_btn3-color-primary-bg">
                  View {{ $typeOptions[$typeKey] ?? 'Group' }}
                </a>
              @endif
            </div>

            @if($isPeopleGroup)
              {{-- PEOPLE GRID (equal-height cards, no row break issues from long names/roles) --}}
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
                          @if($m->facebook)
                            <a href="{{ $m->facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                          @endif
                          @if($m->twitter)
                            <a href="{{ $m->twitter }}" target="_blank" rel="noopener" aria-label="X/Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                          @endif
                          @if($m->linkedin)
                            <a href="{{ $m->linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                          @endif
                          @if($m->instagram)
                            <a href="{{ $m->instagram }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                          @endif
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

          </div>
        @endif
      @endforeach

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

    /* ---------- Team body (fixed rhythm) ---------- */
    #about-team .ac-team-body{
      padding:14px 12px 16px;
      text-align:center;
      background:#eee;
      display:flex;
      flex-direction:column;
      flex:1 1 auto;
      min-height:170px; /* keeps bottom alignment stable */
    }

    /* Clamp LONG NAMES (main issue) */
    #about-team .ac-team-name{
      font-weight:800;
      line-height:1.25;
      margin-bottom:6px;
      color:#243746;
      overflow-wrap:anywhere;
      word-break:break-word;

      display:-webkit-box;
      -webkit-box-orient:vertical;
      -webkit-line-clamp:2;  /* max 2 lines */
      overflow:hidden;
      min-height:calc(1.25em * 2); /* reserve 2 lines on all cards */
    }

    /* Clamp roles too */
    #about-team .ac-team-role{
      opacity:.82;
      line-height:1.35;
      margin-bottom:12px;
      color:#5f6f7c;
      overflow-wrap:anywhere;
      word-break:break-word;

      display:-webkit-box;
      -webkit-box-orient:vertical;
      -webkit-line-clamp:2;  /* max 2 lines */
      overflow:hidden;
      min-height:calc(1.35em * 2); /* reserve 2 lines */
    }

    #about-team .ac-team-role.ac-empty-role{
      visibility:hidden;
    }

    /* Reserve social row height even if empty */
    #about-team .ac-team-social{
      margin-top:auto;
      display:flex;
      justify-content:center;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      min-height:40px; /* keeps card bottoms aligned */
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
      background:rgba(255,255,255,.45);
      text-decoration:none;
      border-radius:10px;
    }

    /* ---------- Partner cards ---------- */
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

    /* ---------- Defensive spacing ---------- */
    #about-team .ac-team-group + .ac-team-group{
      border-top:1px solid rgba(0,0,0,.04);
      padding-top:8px;
    }

    @media (max-width: 575.98px){
      #about-team .ac-team-photo{ height:240px; }
      #about-team .ac-team-body{ min-height:160px; padding:12px 10px 14px; }
    }
  </style>
</section>

@include('site.footers.foot')