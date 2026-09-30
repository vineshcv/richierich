@extends('layouts.admin')

@php $isEdit = $category->exists; @endphp

@section('title', $isEdit ? 'Edit category' : 'Add category')
@section('heading', $isEdit ? 'Edit category' : 'Add category')
@section('subheading', 'Category name and image used on the storefront')
@section('actions')
  <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
<div class="card">
  <form method="POST"
        action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
        enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="field">
        <label for="name">Name *</label>
        <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required />
        @error('name')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="sort_order">Sort order</label>
        <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" />
      </div>

      <div class="field full">
        <label for="description">Description</label>
        <input id="description" type="text" name="description" value="{{ old('description', $category->description) }}" />
      </div>

      <div class="field" style="display:flex;align-items:end;">
        <div class="check-row">
          <input type="hidden" name="is_active" value="0" />
          <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) />
          <label for="is_active">Active on storefront</label>
        </div>
      </div>

      <div class="field full">
        <label for="image">Category image {{ $isEdit ? '(optional — upload to replace)' : '' }}</label>
        <input id="image" type="file" name="image" accept="image/*" />
        <p class="muted" style="font-size:0.8rem;margin:0.35rem 0 0;">Max 2MB.</p>
        @error('image')<div class="error">{{ $message }}</div>@enderror

        @if($isEdit && $category->image_url)
          <div style="margin-top:0.75rem;">
            <img class="thumb" src="{{ $category->image_url }}" alt="" style="width:96px;height:96px;" />
          </div>
        @endif
      </div>
    </div>

    <div style="margin-top:1.25rem;">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update category' : 'Create category' }}</button>
    </div>
  </form>
</div>
@endsection
