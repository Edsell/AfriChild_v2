@include('site.headers.head', ['title' => ($item->meta_title ?? $item->title)])

@include('site.headers.pg-crumb', [
  'title' => 'Project Details',
  'items' => [
    ['label' => 'Projects', 'url' => url('/projects')],
  ]
])

<main id="main" class="site-main">
  <section class="vc_row wpb_row vc_row-fluid vc-zozo-section typo-default">
    <div class="zozo-vc-main-row-inner vc-normal-section">
      <div class="container">
        <div class="row">

          <div class="wpb_column vc_main_column vc_column_container vc_col-sm-12 typo-default">
            <div class="vc_column-inner">
              <div class="wpb_wrapper">

                <div class="section-title-wrapper text-center mb-4">
                  <h2 class="section-title">{{ $item->title }}</h2>
                  @if(!empty($item->excerpt))
                    <h4 class="section-sub-title">{{ $item->excerpt }}</h4>
                  @endif
                </div>

                <div class="row g-4 align-items-start">

                  {{-- Cover image --}}
                  <div class="col-lg-5">
                    @php
                      $cover = $item->cover ? asset('uploads/'.$item->cover) : null;
                    @endphp

                    @if($cover)
                      <div class="project-cover mb-3">
                        <img src="{{ $cover }}" alt="{{ $item->title }}" class="img-responsive w-100" style="border-radius:10px;">
                      </div>
                    @endif

                    {{-- Optional quick meta --}}
                    <div class="card project-summary-card">
  <div class="card-body">
    <div class="project-summary-header">
      <div class="project-summary-heading">
        <span class="project-summary-iconwrap">
          <i class="fa-solid fa-briefcase"></i>
        </span>
        <div>
          <div class="project-summary-title">Project Summary</div>
          <div class="project-summary-subtitle">Key details at a glance</div>
        </div>
      </div>

      @php
        $sd = $item->start_date ?? null;
        $ed = $item->end_date ?? null;
      @endphp

      @if(!empty($sd) && !empty($ed))
        <span class="project-summary-badge">
          <i class="fa-regular fa-clock me-1"></i>
          {{ \Carbon\Carbon::parse($sd)->diffInDays(\Carbon\Carbon::parse($ed)) + 1 }} days
        </span>
      @endif
    </div>

    <div class="project-summary-grid">

      @if(!empty($item->client))
        <div class="project-summary-tile">
          <div class="project-summary-tile-icon">
            <i class="fa-solid fa-user-tie"></i>
          </div>
          <div class="project-summary-tile-body">
            <div class="project-summary-label">Client</div>
            <div class="project-summary-value">{{ $item->client }}</div>
          </div>
        </div>
      @endif

      @if(!empty($item->location))
        <div class="project-summary-tile">
          <div class="project-summary-tile-icon">
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <div class="project-summary-tile-body">
            <div class="project-summary-label">Location</div>
            <div class="project-summary-value">{{ $item->location }}</div>
          </div>
        </div>
      @endif

      @if(!empty($item->start_date))
        <div class="project-summary-tile">
          <div class="project-summary-tile-icon">
            <i class="fa-regular fa-calendar-plus"></i>
          </div>
          <div class="project-summary-tile-body">
            <div class="project-summary-label">Start date</div>
            <div class="project-summary-value">{{ optional($item->start_date)->format('M d, Y') }}</div>
          </div>
        </div>
      @endif

      @if(!empty($item->end_date))
        <div class="project-summary-tile">
          <div class="project-summary-tile-icon">
            <i class="fa-regular fa-calendar-check"></i>
          </div>
          <div class="project-summary-tile-body">
            <div class="project-summary-label">End date</div>
            <div class="project-summary-value">{{ optional($item->end_date)->format('M d, Y') }}</div>
          </div>
        </div>
      @endif

    </div>
  </div>
</div>
                  </div>

                  {{-- Content --}}
                  <div class="col-lg-7">
                    <div class="project-content" style="text-align: justify">
                      @if(!empty($item->description))
                        {!! $item->description !!}
                      @else
                        <p class="text-muted">No description has been added for this project yet.</p>
                      @endif
                    </div>

                    {{-- Optional lightbox image --}}
                    @if($item->galleryImages && $item->galleryImages->count())
                    <div class="mt-4">
                        {{-- <h4 class="mb-3">Project Gallery</h4> --}}

                        <div class="row g-3">
                        @foreach($item->galleryImages as $img)
                            <div class="col-6 col-md-3">
                            <a href="{{ asset('uploads/'.$img->image) }}" target="_blank" rel="noopener">
                                <img src="{{ asset('uploads/'.$img->image) }}"
                                    alt="{{ $item->title }}"
                                    class="img-responsive w-100"
                                    style="border-radius:10px; width:100%; height:120px; object-fit:cover;">
                            </a>
                            </div>
                        @endforeach
                        </div>
                    </div>
                    @endif


                    {{-- <div class="mt-4">
                      <a href="{{ url('/') }}" class="btn btn-default">Back to Home</a>
                      <a href="{{ url('/projects') }}" class="btn btn-outline-secondary" style="margin-left:8px;">
                        View Projects
                      </a>
                    </div> --}}
                  </div>

                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</main>

@include('site.footers.foot')


<style>
  /* Project Summary Card */
.project-summary-card{
  border-radius: 16px;
  border: 1px solid rgba(0,0,0,.06);
  box-shadow: 0 12px 30px rgba(0,0,0,.06);
  overflow: hidden;
  position: relative;
  background: #fff;
}

.project-summary-card::before{
  content:"";
  position:absolute;
  left:0; top:0; bottom:0;
  width: 6px;
  background: linear-gradient(180deg, rgba(225,0,122,.95), rgba(82,195,203,.95));
}

.project-summary-card .card-body{
  padding: 18px 18px 16px 18px;
}

/* Header */
.project-summary-header{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap: 12px;
  padding-left: 6px; /* space from accent bar */
  margin-bottom: 14px;
}

.project-summary-heading{
  display:flex;
  gap: 10px;
  align-items:center;
}

.project-summary-iconwrap{
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display:flex;
  align-items:center;
  justify-content:center;
  background: rgba(225,0,122,.10);
  border: 1px solid rgba(225,0,122,.18);
  flex: 0 0 auto;
}

.project-summary-iconwrap i{
  font-size: 16px;
}

.project-summary-title{
  font-weight: 900;
  font-size: 15px;
  letter-spacing: .2px;
  line-height: 1.1;
}

.project-summary-subtitle{
  font-size: 12px;
  opacity: .65;
  margin-top: 3px;
  font-weight: 600;
}

/* Badge */
.project-summary-badge{
  font-size: 12px;
  padding: 7px 10px;
  border-radius: 999px;
  background: rgba(225,0,122,.10);
  color: rgba(225,0,122,1);
  border: 1px solid rgba(225,0,122,.18);
  font-weight: 800;
  white-space: nowrap;
  margin-left: auto;
  display: inline-flex;
  align-items: center;
}

/* Grid */
.project-summary-grid{
  padding-left: 6px; /* space from accent bar */
  display:grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

@media (max-width: 576px){
  .project-summary-header{ flex-direction:column; align-items:flex-start; }
  .project-summary-badge{ margin-left: 0; }
  .project-summary-grid{ grid-template-columns: 1fr; }
}

/* Tiles */
.project-summary-tile{
  display:flex;
  gap: 10px;
  padding: 12px 12px;
  border-radius: 14px;
  background: rgba(0,0,0,.03);
  border: 1px solid rgba(0,0,0,.06);
  transition: transform .15s ease, box-shadow .15s ease;
}

.project-summary-tile:hover{
  transform: translateY(-1px);
  box-shadow: 0 10px 18px rgba(0,0,0,.06);
}

.project-summary-tile-icon{
  width: 38px;
  height: 38px;
  border-radius: 12px;
  display:flex;
  align-items:center;
  justify-content:center;
  background: rgba(255,255,255,.95);
  border: 1px solid rgba(0,0,0,.06);
  flex: 0 0 auto;
}

.project-summary-tile-icon i{
  font-size: 16px;
  opacity: .9;
}

.project-summary-label{
  font-size: 12px;
  opacity: .7;
  font-weight: 800;
  margin-bottom: 2px;
}

.project-summary-value{
  font-size: 14px;
  font-weight: 900;
  line-height: 1.2;
  word-break: break-word;
}
</style>