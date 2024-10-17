<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\SetupIntent;
use App\Models\User;

class SubscriptionController extends Controller
{
   /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $intent = Auth::user()->createSetupIntent();
        return view('subscription.create', compact('intent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->user()->newSubscription(
            'premium_plan',
            env('STRIPE_ID'))->create($request->paymentMethodId);

        return redirect()->route('home')->with('flash_message', '有料プランへの登録が完了しました。');
    }

    public function edit()
    {
        $user = Auth::user();
        $intent = Auth::user()->createSetupIntent();

        return view('subscription.edit', compact('user', 'intent'));
    }

    public function update(Request $request,User $user)
    {
        Auth::user()->updateDefaultPaymentMethod($request->input('paymentMethodId'));

        return redirect()->route('home')->with('flash_message', 'お支払い方法を変更しました。');

    }

    public function cancel()
    {
        return view('subscription.cancel');
    }

    public function destroy()
    {
        Auth::user()->subscription('premium_plan')->cancelNow();

        return redirect()->route('home')->with('flash_message', 'サブスクリプションを解約しました。');
    }
}