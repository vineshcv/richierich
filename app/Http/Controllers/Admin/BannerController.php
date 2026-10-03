<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\StoreBannerRequest;
use App\Http\Requests\Banner\UpdateBannerRequest;
use App\Models\Banner;
use App\Services\BannerService;
use App\Services\CurrentStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(
        private BannerService $banners,
        private CurrentStore $current,
    ) {
    }

    public function index(): View
    {
        return view('admin.banners.index', [
            'banners' => $this->banners->list(),
        ]);
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $this->banners->create($request->safe()->except('image'), $request->file('image'));

        return back()->with('success', 'Banner added.');
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        abort_unless($this->current->owns($banner), 404);

        $this->banners->update($banner, $request->safe()->except('image'), $request->file('image'));

        return back()->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        abort_unless($this->current->owns($banner), 404);

        $this->banners->delete($banner);

        return back()->with('success', 'Banner deleted.');
    }
}
