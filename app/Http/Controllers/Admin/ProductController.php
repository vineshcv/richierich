<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\CurrentStore;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $products,
        private CategoryService $categories,
        private CurrentStore $current,
    ) {
    }

    public function index(Request $request): View
    {
        return view('admin.products.index', [
            'products' => $this->products->list($request->only(['q', 'category_id', 'status', 'per_page'])),
            'categories' => $this->categories->search(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => $this->categories->search(),
            'chooseStore' => $this->current->canSwitch() && ! $this->current->adminId(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        if ($this->current->canSwitch() && ! $this->current->adminId()) {
            return back()->withInput()->withErrors([
                'store' => 'Choose a store above before adding a product.',
            ]);
        }

        $payload = $request->safe()->except('images');
        if ($storeId = $this->current->adminId()) {
            $payload['store_id'] = $storeId;
        }

        $this->products->create($payload, $request->file('images') ?: []);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        abort_unless($this->current->owns($product), 404);

        return view('admin.products.form', [
            'product' => $product->load('images'),
            'categories' => $this->categories->search(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        abort_unless($this->current->owns($product), 404);

        $payload = $request->safe()->except('images');
        $payload['remove_image_ids'] = $request->input('remove_image_ids', []);
        $payload['primary_image_id'] = $request->input('primary_image_id');
        $payload['replace_images'] = $request->boolean('replace_images');

        $this->products->update($product, $payload, $request->file('images') ?: []);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        abort_unless($this->current->owns($product), 404);

        $this->products->delete($product);

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}
