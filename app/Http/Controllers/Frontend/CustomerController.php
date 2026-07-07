<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Address;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Rules\BdPhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerController
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function dashboard(): View
    {
        $user = auth()->user();

        return view('customer.dashboard', [
            'recentOrders' => $this->orderRepository->getByUser($user->id, 5),
        ]);
    }

    public function orders(): View
    {
        return view('customer.orders', [
            'orders' => $this->orderRepository->getByUser(auth()->id()),
        ]);
    }

    public function orderShow(string $orderNumber): View
    {
        $order = auth()->user()->orders()
            ->with(['items.product', 'items.variant', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('customer.order-show', compact('order'));
    }

    public function profile(): View
    {
        return view('customer.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        auth()->user()->update(['password' => Hash::make($request->validated('password'))]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function addresses(): View
    {
        return view('customer.addresses', ['addresses' => auth()->user()->addresses]);
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string',
            'full_name' => 'required|string|max:255',
            'phone' => ['required', 'string', new BdPhone],
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'division' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        auth()->user()->addresses()->create($validated);

        return back()->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, Address $address): RedirectResponse
    {
        abort_if($address->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'label' => 'required|string',
            'full_name' => 'required|string|max:255',
            'phone' => ['required', 'string', new BdPhone],
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'division' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'Address updated successfully.');
    }

    public function destroyAddress(Address $address): RedirectResponse
    {
        abort_if($address->user_id !== auth()->id(), 403);
        $address->delete();

        return back()->with('success', 'Address removed successfully.');
    }
}
