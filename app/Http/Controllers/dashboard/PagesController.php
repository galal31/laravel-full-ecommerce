<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Services\dashboard\PageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        protected PageService $page_service
    )
    {
        //
    }
    public function index()
    {
        if (request()->ajax()){
            return $this->page_service->PagesDataTable();
        }
        return view('dashboard.pages.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePage($request);

        $this->page_service->createPage($validated);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', __('pages.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $page = $this->page_service->getPage($id);

        abort_if(! $page, 404);

        return view('dashboard.pages.page', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = $this->page_service->getPage($id);

        abort_if(! $page, 404);

        return view('dashboard.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $page = $this->page_service->getPage($id);

        abort_if(! $page, 404);

        $validated = $this->validatePage($request, $id);

        $this->page_service->updatePage($id, $validated);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', __('pages.updated_successfully'));
    }

    private function validatePage(Request $request, ?string $id = null)
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')->ignore($id),
            ],
            'content' => ['required', 'string'],
        ], [
            'slug.regex' => __('pages.slug_format_error'),
        ], [
            'title' => __('pages.title'),
            'slug' => __('pages.slug'),
            'content' => __('pages.content'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $page = $this->page_service->getPage($id);

        abort_if(! $page, 404);

        $this->page_service->deletePage($id);

        return redirect()
            ->route('dashboard.pages.index')
            ->with('success', __('pages.deleted_successfully'));
    }
}
