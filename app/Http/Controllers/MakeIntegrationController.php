<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MakeIntegrationController extends Controller
{
    public function sendCode()
    {

        $user = User::where('email', Auth::user()->email)->first();
        
        $code = rand(100000, 999999);

        $user->code = $code;
        $user->save();

        Http::post('YOURWEBHOOK', [
            'to' => $user->email,
            'name' => $user->name,
            'code' => $code,
        ]);

        return redirect()->back()->with('status', 'verification-link-sent');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|integer|digits:6',
        ]);

        $user = User::where('email', Auth::user()->email)
                    ->where('code', $request->code)
                    ->first();

        if (!$user) {
            return redirect()->back()->withErrors(['code' => 'The verification code is invalid.'])->withInput();
        }

        $user->email_verified_at = now();
        $user->code = null;
        $user->save();

        return redirect()->back()->with('status', 'veriftication-status');
    }

    public function sendFeedback(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string',
            'feedback' => 'required|string',
        ]);

        $email_to = 'COMPANY EMAIL'; // YOUR COMPANY EMAIL
        
        Http::post('YOURWEBHOOK', [
            'from' => Auth::user()->email,
            'to' => $email_to,
            'name' => $request->name,
            'feedback' => $request->feedback,
        ]);

        return redirect()->back();

    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        if($product) {

            Http::post('https://hook.eu2.make.com/qx9oxhbwoc7xtmpfkne89k7qkmhqkrrx', [
                'product_name' => $request->product_name,
                'category_id' => $request->category_id,
                'buying_price' => $request->buying_price,
                'selling_price' => $request->selling_price,
                'stock' => $request->stock,
            ]);

            return redirect()->back();
        }
    }
}