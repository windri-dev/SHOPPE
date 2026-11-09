<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withCount('orders')
            ->withSum('orders', 'grand_total')
            ->whereIn('role', [UserRole::CUSTOMER, UserRole::USER])
            ->latest();

        // Search by name, email, phone, address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(12)->withQueryString();

        $stats = [
            'total_customers' => User::whereIn('role', [UserRole::CUSTOMER, UserRole::USER])->count(),
            'active_buyers' => User::whereHas('orders')->count(),
            'total_members' => User::count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }
}
