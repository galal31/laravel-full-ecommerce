<?php

namespace App\Services\dashboard;

use App\Reposetories\dashboard\PageRepo;
use Yajra\DataTables\Facades\DataTables;

class PageService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected PageRepo $pageRepo)
    {
        //
    }

    public function PagesDataTable()
    {
        $pages = $this->pageRepo->getPages();

        return DataTables::of($pages)
            ->addIndexColumn()
            ->editColumn('title', function ($page) {
                return e($page->title);
            })
            ->editColumn('slug', function ($page) {
                return '<a href="'.route('dashboard.pages.show', $page->id).'">'.e($page->slug).'</a>';
            })
            ->editColumn('content', function ($page) {
                return (string) str($page->content)->stripTags()->limit(80);
            })
            ->editColumn('created_at', function ($page) {
                return $page->created_at?->format('Y-m-d h:i A');
            })
            ->addColumn('actions', function ($page) {
                return '
                    <a href="'.route('dashboard.pages.show', $page->id).'" class="btn btn-sm btn-outline-info" title="'.__('datatables.show').'">
                        <i class="ft-eye"></i>
                    </a>
                    <a href="'.route('dashboard.pages.edit', $page->id).'" class="btn btn-sm btn-outline-primary" title="'.__('datatables.edit').'">
                        <i class="ft-edit"></i>
                    </a>
                    <form action="'.route('dashboard.pages.destroy', $page->id).'" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="'.__('datatables.delete').'" onclick="return confirm(\''.__('pages.delete_confirmation').'\')">
                            <i class="ft-trash-2"></i>
                        </button>
                    </form>
                ';
            })
            ->rawColumns(['slug', 'actions'])
            ->make(true);
    }

    public function createPage(array $data)
    {
        return $this->pageRepo->createPage($data);
    }

    public function updatePage($id, array $data)
    {
        return $this->pageRepo->updatePage($id, $data);
    }

    public function getPage($id)
    {
        $page = $this->pageRepo->getPage($id);

        return $page;
    }

    public function deletePage($id)
    {
        return $this->pageRepo->deletePage($id);
    }
}
