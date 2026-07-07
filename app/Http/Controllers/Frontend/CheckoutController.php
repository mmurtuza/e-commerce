<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\CheckoutRequest;
use App\Payments\PaymentManager;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\ShippingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
        private readonly ShippingService $shippingService,
        private readonly PaymentManager $paymentManager,
    ) {}

    public function index(): View
    {
        $cart = $this->cartService->getCart()->load('items.product.images', 'coupon');

        return view('checkout.index', [
            'cart' => $cart,
            'addresses' => auth()->user()->addresses,
            'divisions' => $this->shippingService->getDivisions(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        try {
            $cart = $this->cartService->getCart()->load('items.product');
            $data = $request->validated();

            if ($request->boolean('save_address')) {
                auth()->user()->addresses()->create([
                    'label' => 'home',
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'],
                    'address_line_1' => $data['address_line_1'],
                    'address_line_2' => $data['address_line_2'] ?? null,
                    'city' => $data['city'],
                    'district' => $data['district'],
                    'division' => $data['division'],
                    'postal_code' => $data['postal_code'] ?? null,
                ]);
            }

            $order = $this->checkoutService->placeOrder(
                auth()->user(),
                $cart,
                $data,
                $data['payment_method'],
            );

            try {
                $paymentResult = $this->paymentManager->driver($order->payment_method->value)->initiate($order);

                $this->cartService->clear();

                return redirect($paymentResult['redirect']);
            } catch (\Throwable $e) {
                $this->checkoutService->rollbackOrder($order);
                throw $e;
            }
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function success(string $orderNumber): View
    {
        $order = auth()->user()->orders()
            ->with(['items.product', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}
