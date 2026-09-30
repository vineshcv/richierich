@extends('layouts.admin')

@php
  $isEdit = $product->exists;
@endphp

@section('title', $isEdit ? 'Edit product' : 'Add product')
@section('heading', $isEdit ? 'Edit product' : 'Add product')
@section('subheading', $isEdit ? 'Update details, images and visibility' : 'Add a new item to the catalogue')
@section('actions')
  <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
<div class="card">
  <form method="POST"
        action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
        enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="field full">
        <label for="name">Name *</label>
        <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required />
        @error('name')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field full">
        <label for="description">Description</label>
        <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
        @error('description')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="category_search">Category (search or type new) *</label>
        <input id="category_search" list="category-list" type="text"
               value="{{ old('category_name', $product->category?->name) }}"
               placeholder="Start typing…" autocomplete="off" />
        <datalist id="category-list">
          @foreach($categories as $category)
            <option value="{{ $category->name }}" data-id="{{ $category->id }}"></option>
          @endforeach
        </datalist>
        <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id', $product->category_id) }}" />
        <input type="hidden" name="category_name" id="category_name" value="{{ old('category_name', $product->category?->name) }}" />
        <p class="muted" style="font-size:0.8rem;margin:0.35rem 0 0;">Pick an existing category or enter a new name to create one.</p>
        @error('category_id')<div class="error">{{ $message }}</div>@enderror
        @error('category_name')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="designer">Designer</label>
        <input id="designer" type="text" name="designer" value="{{ old('designer', $product->designer) }}" />
      </div>

      <div class="field">
        <label for="fabric">Fabric</label>
        <input id="fabric" type="text" name="fabric" value="{{ old('fabric', $product->fabric) }}" />
      </div>

      <div class="field">
        <label for="fit">Fit</label>
        <input id="fit" type="text" name="fit" value="{{ old('fit', $product->fit) }}" />
      </div>

      <div class="field">
        <label for="colors">Colors <span class="muted">(comma-separated)</span></label>
        <input id="colors" type="text" name="colors"
               value="{{ old('colors', is_array($product->colors) ? implode(', ', $product->colors) : '') }}"
               placeholder="Red, Gold, Ivory" />
      </div>

      <div class="field">
        <label for="available_sizes">Available sizes <span class="muted">(comma-separated)</span></label>
        <input id="available_sizes" type="text" name="available_sizes"
               value="{{ old('available_sizes', is_array($product->available_sizes) ? implode(', ', $product->available_sizes) : '') }}"
               placeholder="S, M, L, Free size" />
      </div>

      <div class="field">
        <label for="price">Price *</label>
        <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? 0) }}" required />
        @error('price')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="sku">SKU</label>
        <input id="sku" type="text" name="sku" value="{{ old('sku', $product->sku) }}" />
        @error('sku')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status">
          @foreach(['active','draft','archived'] as $st)
            <option value="{{ $st }}" @selected(old('status', $product->status ?? 'active') === $st)>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
      </div>

      <div class="field" style="display:flex;align-items:end;">
        <div class="check-row">
          <input type="hidden" name="show_price" value="0" />
          <input id="show_price" type="checkbox" name="show_price" value="1" @checked(old('show_price', $product->show_price ?? true)) />
          <label for="show_price">Show price on storefront</label>
        </div>
      </div>

      <div class="field full">
        <label for="care_instructions">Care instructions</label>
        <textarea id="care_instructions" name="care_instructions">{{ old('care_instructions', $product->care_instructions) }}</textarea>
      </div>

      <div class="field full">
        <label for="admin_note">Internal note <span class="muted">(admin only)</span></label>
        <textarea id="admin_note" name="admin_note" rows="4" placeholder="Supplier, restock, packing notes…">{{ old('admin_note', $product->admin_note) }}</textarea>
        <p class="muted" style="font-size:0.8rem;margin:0.35rem 0 0;">Visible only in admin. Never shown on the shop or product page.</p>
        @error('admin_note')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field full">
        <label for="tags">Tags <span class="muted">(comma-separated)</span></label>
        <input id="tags" type="text" name="tags"
               value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '') }}"
               placeholder="festive, new arrival" />
      </div>

      <div class="field full">
        <label for="images">Product images</label>
        <p class="muted" style="font-size:0.85rem;margin:0 0 0.65rem;">Upload several photos for the product page. Shoppers can zoom in on each one. First image is the cover unless you pick Primary.</p>
        <label class="image-dropzone" for="images" id="image-dropzone">
          <input id="images" type="file" name="images[]" accept="image/*" multiple />
          <strong>Drop images or click to browse</strong>
          <span>JPG, PNG or WebP · up to 10 photos · 4MB each</span>
        </label>
        @error('images')<div class="error">{{ $message }}</div>@enderror
        @error('images.*')<div class="error">{{ $message }}</div>@enderror
        <div class="image-pending" id="image-pending" hidden></div>

        @if($isEdit && $product->images->isNotEmpty())
          <div class="check-row" style="margin-top:0.85rem;">
            <input type="hidden" name="replace_images" value="0" />
            <input id="replace_images" type="checkbox" name="replace_images" value="1" @checked(old('replace_images')) />
            <label for="replace_images">Replace all current images with the new upload</label>
          </div>

          <p style="margin:1rem 0 0.5rem;font-size:0.9rem;">Current images — choose primary, or mark delete:</p>
          <div class="product-image-grid">
            @foreach($product->images as $image)
              @php $url = \Illuminate\Support\Facades\Storage::disk('public')->url($image->path); @endphp
              <div class="product-image-card">
                <img src="{{ $url }}" alt="" />
                <div class="check-row" style="margin:0.45rem 0 0;justify-content:center;">
                  <input type="radio" name="primary_image_id" id="primary_{{ $image->id }}" value="{{ $image->id }}"
                         @checked(old('primary_image_id', $product->images->firstWhere('is_primary', true)?->id) == $image->id) />
                  <label for="primary_{{ $image->id }}" style="font-size:0.75rem;">Primary</label>
                </div>
                <div class="check-row" style="margin:0.25rem 0 0;justify-content:center;">
                  <input type="checkbox" name="remove_image_ids[]" id="remove_{{ $image->id }}" value="{{ $image->id }}" />
                  <label for="remove_{{ $image->id }}" style="font-size:0.75rem;color:#b91c1c;">Delete</label>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div style="margin-top:1.25rem;">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update product' : 'Create product' }}</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const search = document.getElementById('category_search');
  const categoryId = document.getElementById('category_id');
  const categoryName = document.getElementById('category_name');
  const options = Array.from(document.querySelectorAll('#category-list option'));

  function syncCategory() {
    const value = (search.value || '').trim();
    const match = options.find(o => o.value.toLowerCase() === value.toLowerCase());
    if (match) {
      categoryId.value = match.getAttribute('data-id') || '';
      categoryName.value = '';
    } else {
      categoryId.value = '';
      categoryName.value = value;
    }
  }

  search?.addEventListener('input', syncCategory);
  search?.addEventListener('change', syncCategory);
  syncCategory();

  const input = document.getElementById('images');
  const pending = document.getElementById('image-pending');
  const zone = document.getElementById('image-dropzone');
  function renderPending() {
    if (!input || !pending) return;
    const files = Array.from(input.files || []);
    pending.hidden = files.length === 0;
    pending.innerHTML = files.map(function (file) {
      const url = URL.createObjectURL(file);
      return '<div class="product-image-card is-pending"><img src="' + url + '" alt=""><span>' +
        String(file.name).replace(/</g, '&lt;') + '</span></div>';
    }).join('');
  }
  input?.addEventListener('change', renderPending);
  if (zone && input) {
    ['dragenter', 'dragover'].forEach(function (ev) {
      zone.addEventListener(ev, function (e) {
        e.preventDefault();
        zone.classList.add('is-over');
      });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
      zone.addEventListener(ev, function (e) {
        e.preventDefault();
        zone.classList.remove('is-over');
      });
    });
    zone.addEventListener('drop', function (e) {
      if (!e.dataTransfer || !e.dataTransfer.files.length) return;
      input.files = e.dataTransfer.files;
      renderPending();
    });
  }
})();
</script>
@endpush
