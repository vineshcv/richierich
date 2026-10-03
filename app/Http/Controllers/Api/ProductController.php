<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\CurrentStore;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $products,
        private CurrentStore $current,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->products->list($request->only(['category_id', 'status', 'q', 'per_page']))
        );
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product->load(['category', 'images'])]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->products->create(
            $request->safe()->except('images'),
            $request->file('images') ?: []
        );

        return response()->json(['data' => $product->makeVisible('admin_note')], 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        abort_unless($this->current->owns($product), 404);

        $product = $this->products->update(
            $product,
            $request->safe()->except('images'),
            $request->file('images') ?: []
        );

        return response()->json(['data' => $product->makeVisible('admin_note')]);
    }

    public function destroy(Product $product): JsonResponse
    {
        abort_unless($this->current->owns($product), 404);

        $this->products->delete($product);

        return response()->json(['message' => 'Deleted']);
    }
}
