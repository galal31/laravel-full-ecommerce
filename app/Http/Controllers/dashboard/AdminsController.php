<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Dashboard\Admin;
use App\Services\dashboard\AdminService;
use Illuminate\Http\Request;

class AdminsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $adminsService;
    public function __construct(AdminService $adminsService)
    {
        $this->adminsService = $adminsService;
    }
    public function index()
    {
        $data = $this->adminsService->index();
        return view('dashboard.admins.index',['roles'=>$data['roles'],'admins'=>$data['admins']]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        $data = $request->validated();
        $admin = $this->adminsService->store($data);
        if(!$admin){
            return redirect()->route('dashboard.admins.index')->with('error',__('messages.error'));
        }
        return redirect()->route('dashboard.admins.index')->with('success',__('messages.done'));

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, string $id)
    {
        $admin = $this->adminsService->update($request->validated(),$id);
        if(!$admin){
            return redirect()->route('dashboard.admins.index')->with('error',__('messages.error'));
        }
        return redirect()->route('dashboard.admins.index')->with('success',__('messages.done'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(!$this->adminsService->destroy($id)){
            return redirect()->route('dashboard.admins.index')->with('error',__('messages.error'));
        }
        return redirect()->route('dashboard.admins.index')->with('success',__('messages.done'));
    }

    public function toggleStatus(Request $request, string $id){
        $success = $this->adminsService->toggleStatus($id);
        if ($request->ajax()) {
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => __('messages.done') // رسالة النجاح من ملف الترجمة
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.error') // رسالة الخطأ
            ]);
        }
    }
    }
}
