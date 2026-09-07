<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BugReportController extends Controller
{
    /**
     * Handle bug report submissions from the public form.
     */
    public function store(Request $request)
    {
        $validated = $request->validateWithBag('bugReport', [
            'reporter_name' => 'required|string|max:255',
            'reporter_email' => 'required|email|max:255',
            'summary' => 'required|string|max:150',
            'description' => 'required|string|max:2000',
            'steps' => 'nullable|string|max:2000',
            'impact' => 'nullable|string|max:120',
            'attachments.*' => 'nullable|mimes:jpg,jpeg,png,webp,gif,pdf|max:4096',
        ]);

        $supportEmail = BusinessSetting::get('support_email') ?: config('mail.from.address');
        if (! $supportEmail) {
            return back()->withErrors(['description' => __('ui.hlasenie_sa_neda_odoslat_prevadzka_nema')], 'bugReport')->withInput();
        }

        $payload = [
            'name' => $validated['reporter_name'],
            'email' => $validated['reporter_email'],
            'summary' => $validated['summary'],
            'description' => $validated['description'],
            'steps' => $validated['steps'] ?? null,
            'impact' => $validated['impact'] ?? null,
            'browser' => $request->header('User-Agent'),
            'reported_at' => now()->format('d.m.Y H:i'),
        ];

        Mail::send('emails.bug-report', ['data' => $payload], function ($message) use ($supportEmail, $request) {
            $message->to($supportEmail)
                ->replyTo($request->reporter_email, $request->reporter_name)
                ->subject('[Bug Report] '.$request->summary);

            foreach ((array) $request->file('attachments', []) as $file) {
                if ($file) {
                    $message->attach($file->getRealPath(), [
                        'as' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                    ]);
                }
            }
        });

        return back()->with('bug_report_success', 'Ďakujeme, chybu sme zaznamenali a dáme vám vedieť čo najskôr.');
    }
}
