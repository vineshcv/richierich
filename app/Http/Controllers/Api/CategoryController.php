<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categories)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->categories->search($request->query('q'), $request->boolean('active_only'));

        return response()->json(['data' => $items]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categories->create(
            $request->safe()->except('image'),
            $request->file('image')
        );

        return response()->json(['data' => $category], 201);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categories->delete($category);

        return response()->json(['message' => 'Deleted']);
    }
}
