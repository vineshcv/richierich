<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\StoreBannerRequest;
use App\Models\Banner;
use App\Services\BannerService;
use App\Services\CurrentStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(
        private BannerService $banners,
        private CurrentStore $current,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->banners->list($request->boolean('active_only')),
        ]);
    }

    public function store(StoreBannerRequest $request): JsonResponse
    {
        $banner = $this->banners->create(
            $request->safe()->except('image'),
            $request->file('image')
        );

        return response()->json(['data' => $banner], 201);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        abort_unless($this->current->owns($banner), 404);

        $this->banners->delete($banner);

        return response()->json(['message' => 'Deleted']);
    }
}
