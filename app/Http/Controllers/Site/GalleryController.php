<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryItem;

class GalleryController extends Controller
{
  public function index()
  {
    $galleries = Gallery::where('is_active', 1)
      ->withCount(['items' => fn($q) => $q->where('is_active', 1)])
      ->orderBy('sort_order')
      ->orderBy('name')
      ->get();

    $items = GalleryItem::where('is_active', 1)
      ->with('gallery:id,name,slug')
      ->whereHas('gallery', fn($q) => $q->where('is_active', 1))
      ->orderBy('sort_order')
      ->orderByDesc('id')
      ->paginate(12)
      ->withQueryString();

    return view('site.gallery', compact('galleries', 'items'));
  }

  public function show(Gallery $gallery)
  {
    abort_unless($gallery->is_active, 404);

    $galleries = Gallery::where('is_active', 1)
      ->withCount(['items' => fn($q) => $q->where('is_active', 1)])
      ->orderBy('sort_order')
      ->orderBy('name')
      ->get();

    $items = GalleryItem::where('is_active', 1)
      ->where('gallery_id', $gallery->id)
      ->with('gallery:id,name,slug')
      ->orderBy('sort_order')
      ->orderByDesc('id')
      ->paginate(12)
      ->withQueryString();

    $activeSlug = $gallery->slug;

    return view('site.gallery', compact('galleries', 'items', 'activeSlug'));
  }
}