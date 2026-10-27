<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->role === 'super_admin', 403);
        return view('admin.users.index', ['users' => User::query()->latest()->paginate(30), 'roles' => ['super_admin', 'content_manager', 'store_manager', 'sales_manager', 'customer']]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->role === 'super_admin', 403);
        $role = $request->validate(['role' => ['required', 'in:super_admin,content_manager,store_manager,sales_manager,customer']])['role'];
        abort_if($user->id === $request->user()->id && $role !== 'super_admin', 422, 'نمی‌توانید نقش مدیریتی حساب خود را حذف کنید.');
        $user->update(['role' => $role]);
        return back()->with('success', 'نقش کاربر به‌روزرسانی شد.');
    }
}
