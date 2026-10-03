@extends('layouts.admin')

@section('title', 'Banners')
@section('heading', 'Banners')
@section('subheading')
  Home carousel — 1MB each
@endsection

@section('content')
<div class="card">
  <p class="muted" style="margin-top:0;">Edit a banner below, or replace its image. Changes show on the storefront immediately.</p>

  @forelse($banners as $banner)
    <div class="card" style="box-shadow:none;margin-bottom:0.85rem;">
      <div class="form-grid" style="align-items:start;">
        <div class="field">
          <img class="thumb" src="{{ $banner->image_url }}" alt="" style="width:100%;max-width:280px;height:140px;object-fit:cover;" />
        </div>
        <div class="field full">
          <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="form-grid" style="margin:0;">
            @csrf
            @method('PUT')
            <div class="field">
              <label>Title</label>
              <input type="text" name="title" value="{{ old('title', $banner->title) }}" />
            </div>
            <div class="field">
              <label>Link URL</label>
              <input type="url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="https://" />
            </div>
            <div class="field">
              <label>Sort order</label>
              <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $banner->sort_order) }}" />
            </div>
            <div class="field" style="display:flex;align-items:end;">
              <div class="check-row">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active)) id="active_{{ $banner->id }}" />
                <label for="active_{{ $banner->id }}">Active</label>
              </div>
            </div>
            <div class="field full">
              <label>Replace image (optional, max 1MB)</label>
              <input type="file" name="image" accept="image/*" />
            </div>
            <div class="field full actions">
              <button type="submit" class="btn btn-primary btn-sm">Save banner</button>
            </div>
          </form>
          <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Delete this banner?')" style="margin-top:0.5rem;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <p class="muted">No banners yet.</p>
  @endforelse
</div>

<div class="card">
  <h2 style="margin-top:0;font-size:1.15rem;">Add banner</h2>
  <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="form-grid">
    @csrf
    <div class="field">
      <label for="title">Title</label>
      <input id="title" type="text" name="title" value="{{ old('title') }}" />
    </div>
    <div class="field">
      <label for="link_url">Link URL</label>
      <input id="link_url" type="url" name="link_url" value="{{ old('link_url') }}" placeholder="https://" />
    </div>
    <div class="field">
      <label for="sort_order">Sort order</label>
      <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}" />
    </div>
    <div class="field" style="display:flex;align-items:end;">
      <div class="check-row">
        <input type="hidden" name="is_active" value="0" />
        <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) />
        <label for="is_active">Active</label>
      </div>
    </div>
    <div class="field full">
      <label for="image">Image * (max 1MB)</label>
      <input id="image" type="file" name="image" accept="image/*" required />
      @error('image')<div class="error">{{ $message }}</div>@enderror
    </div>
    <div class="field full">
      <button type="submit" class="btn btn-primary">Upload banner</button>
    </div>
  </form>
</div>
@endsection
