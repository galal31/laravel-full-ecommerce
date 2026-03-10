<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\dashboard\BrandService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index(Request $request)
    {
        // ليه بنفصل كود الـ AJAX جوه الـ index؟
        // عشان نوفر راوت، لو الـ Request جاي من المتصفح العادي بنرجع الـ View، لو جاي من الـ DataTables بنرجع الداتا JSON.
        if ($request->ajax()) {
            $query = $this->brandService->getBrandsQuery();
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', fn($row) => $row->getTranslation('name', app()->getLocale()))
                ->addColumn('logo', function ($row) {
                    $url = $row->logo ? asset('storage/brands/' . $row->logo) : asset('images/default.png');
                    return '<img src="' . $url . '" alt="logo" width="50" height="50" class="img-thumbnail rounded">';
                })
                ->addColumn('status', function ($row) {
                    $class = $row->getRawOriginal('status') ? 'success' : 'danger';
                    $text = $row->status ;
                    return '<span class="badge badge-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('actions', fn($row) => view('dashboard.brands._actions', ['brand' => $row])->render())
                // ليه بنستخدم rawColumns؟
                // لأن لارافيل بيعمل Escape لعلامات الـ HTML للحماية من الـ XSS، فبنفهمه إن العواميد دي فيها HTML آمن اعرضه زي ما هو.
                ->rawColumns(['logo', 'status', 'actions'])
                ->make(true);
        }

        return view('dashboard.brands.index');
    }

    public function store(Request $request)
    {
        // يفضل عمل FormRequest هنا للـ Validation
        // $request->validate(['name' => 'required', 'logo' => 'image|mimes:jpeg,png,jpg|max:2048']);

        // ليه الـ try..catch هنا مش في الـ Service؟
        // الـ Controller هو نقطة التواصل مع الـ Frontend، لو حصل Exception جوه الـ Service، الكنترولر هيصطاده ويرجع Status 500 بدل ما الـ App يضرب 500 بصفحة HTML تكسر الـ AJAX.
        try {
            $this->brandService->storeBrand($request);
            return response()->json(['success' => true, 'message' => __('brands.added_successfully')]);
        } catch (\Exception $e) {
            // ليه بنعمل Log؟
            // عشان نرجع للمستخدم رسالة شيك، بس إنت كمطور تفتح ملف laravel.log وتعرف سبب الخطأ التقني الحقيقي.
            Log::error('Brand Creation Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->brandService->updateBrand($id, $request);
            return response()->json(['success' => true, 'message' => __('brands.updated_successfully')]);
        } catch (\Exception $e) {
            Log::error('Brand Update Failed (ID: '.$id.'): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->brandService->deleteBrand($id);
            return response()->json(['success' => true, 'message' => __('brands.deleted_successfully')]);
        } catch (\Exception $e) {
            Log::error('Brand Deletion Failed (ID: '.$id.'): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $this->brandService->toggleStatus($id);
            return response()->json(['success' => true, 'message' => __('brands.status_updated')]);
        } catch (\Exception $e) {
            Log::error('Brand Status Toggle Failed (ID: '.$id.'): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }
    }
}