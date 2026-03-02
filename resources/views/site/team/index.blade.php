@php
  $pageTitle = 'Team';
  $crumbTitle = 'Team';
  $crumbItems = [
    ['label' => 'Home', 'url' => route('site.home')],
    ['label' => 'About', 'url' => route('site.about')],
    ['label' => 'Team'],
  ];
@endphp

@include('site.headers.head', ['title' => $pageTitle])
@include('site.headers.pg-crumb', ['title' => $crumbTitle, 'items' => $crumbItems])

<section class="vc_row wpb_row vc_row-fluid vc-zozo-section typo-default" style="padding:55px 0; background:#fafafa;">
  <div class="zozo-vc-main-row-inner vc-normal-section">
    <div class="container">

      <div class="zozo-parallax-header">
        <div class="parallax-header content-style-default">
          <h2 class="parallax-title">Our Team & Partners</h2>
          <div class="parallax-desc default-style" style="max-width:820px;">
            Meet the people and organizations supporting AfriChild’s mission.
          </div>
        </div>
      </div>

      @foreach($typeOrder as $typeKey)
        @php $group = ($teamByType->get($typeKey, collect()))->values(); @endphp

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

            {{-- PEOPLE (Board / Secretariat) --}}
            @if($typeKey === \App\Models\TeamMember::TYPE_BOARD || $typeKey === \App\Models\TeamMember::TYPE_SECRETARIAT)

              {{-- Use Bootstrap gutters for consistent spacing --}}
              <div class="row g-4 ac-grid">
                @foreach($group as $m)
                  @php
                    // If your photos are stored in public/storage, switch to: Storage::url($m->photo)
                    $photoUrl = $m->photo ? asset(ltrim($m->photo,'/')) : null;
                  @endphp

                  {{-- d-flex makes the card stretch equal height --}}
                  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 d-flex">
                    <article class="ac-team-card w-100">
                      <div class="ac-team-photo">
                        @if($photoUrl)
                          <img loading="lazy" decoding="async" src="{{ $photoUrl }}" alt="{{ $m->name }}">
                        @else
                          <div class="ac-photo-fallback">{{ $m->name }}</div>
                        @endif
                      </div>

                      <div class="ac-team-body">
                        <div class="ac-team-name">{{ $m->name }}</div>
                        @if($m->designation)
                          <div class="ac-team-role">{{ $m->designation }}</div>
                        @endif

                        <div class="ac-team-social">
                          @if($m->facebook)
                            <a href="{{ $m->facebook }}" target="_blank" rel="noopener" aria-label="Facebook">
                              <i class="fa-brands fa-facebook-f"></i>
                            </a>
                          @endif
                          @if($m->twitter)
                            <a href="{{ $m->twitter }}" target="_blank" rel="noopener" aria-label="X/Twitter">
                              <i class="fa-brands fa-x-twitter"></i>
                            </a>
                          @endif
                          @if($m->linkedin)
                            <a href="{{ $m->linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn">
                              <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                          @endif
                          @if($m->instagram)
                            <a href="{{ $m->instagram }}" target="_blank" rel="noopener" aria-label="Instagram">
                              <i class="fa-brands fa-instagram"></i>
                            </a>
                          @endif
                        </div>
                      </div>
                    </article>
                  </div>
                @endforeach
              </div>

            @else
            {{-- PARTNERS (logos) --}}

              <div class="row g-4 ac-grid">
                @foreach($group as $m)
                  @php
                    $logoUrl = $m->photo ? asset(ltrim($m->photo,'/')) : null;
                    $link = $m->linkedin ?: ($m->facebook ?: ($m->twitter ?: ($m->instagram ?: null)));
                  @endphp

                  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 d-flex">
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
</section>

<style>
 /* Make body a flex column so we can push socials to bottom */
#about-team .ac-team-body{
  padding: 14px 12px 16px;
  text-align:center;
  background:#eee;
  display:flex;
  flex-direction:column;
  height: 170px;          /* <-- keeps card bottom consistent; adjust if needed */
}

/* Name can wrap but keep it tight */
#about-team .ac-team-name{
  font-weight:800;
  margin-bottom:6px;
  line-height:1.2;
}

/* Clamp designation to 2 lines to keep equal heights */
#about-team .ac-team-role{
  opacity:.8;
  margin-bottom:12px;
  line-height:1.3;

  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;  /* <-- max 2 lines */
  overflow: hidden;
  min-height: calc(1.3em * 2); /* <-- reserves space for 2 lines always */
}

/* Push social icons to bottom consistently */
#about-team .ac-team-social{
  margin-top:auto;        /* <-- pins to bottom */
  display:flex;
  justify-content:center;
  gap:10px;
  flex-wrap:wrap;
}
#about-team .ac-team-card{
  height:100%;
  display:flex;
  flex-direction:column;
}
</style>

@include('site.footers.foot')