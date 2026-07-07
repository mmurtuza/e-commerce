<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PageController
{
    public function about(): View
    {
        return view('pages.about', [
            'page' => Page::where('slug', 'about')->firstOrFail(),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'phone' => Setting::get('phone', '+880 1700-000000'),
            'email' => Setting::get('email', 'support@dinajpuritpark.com'),
            'address' => Setting::get('address', 'Dhaka, Bangladesh'),
        ]);
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        // Queue a simple notification email if mail is configured
        try {
            Mail::raw(
                "Name: {$data['name']}\nEmail: {$data['email']}\n\n{$data['message']}",
                fn ($m) => $m->to(Setting::get('email', 'support@dinajpuritpark.com'))
                    ->subject('Contact: '.$data['subject'])
            );
        } catch (\Throwable) {
            // Silently fail if mail not configured in dev
        }

        return back()->with('success', __('general.message_sent'));
    }

    public function faq(): View
    {
        return view('pages.faq', [
            'page' => Page::where('slug', 'faq')->firstOrFail(),
        ]);
    }

    public function terms(): View
    {
        return view('pages.terms', [
            'page' => Page::where('slug', 'terms')->firstOrFail(),
        ]);
    }

    public function privacy(): View
    {
        return view('pages.privacy', [
            'page' => Page::where('slug', 'privacy')->firstOrFail(),
        ]);
    }

    public function returnPolicy(): View
    {
        return view('pages.return-policy', [
            'page' => Page::where('slug', 'return-policy')->firstOrFail(),
        ]);
    }
}
