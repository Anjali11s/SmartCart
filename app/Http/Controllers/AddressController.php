<?php

namespace App\Http\Controllers;

use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        $addresses = Auth::user()->shippingAddresses;
        return view('addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('addresses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'       => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address_line1'   => 'required|string|max:255',
            'address_line2'   => 'nullable|string|max:255',
            'landmark'        => 'nullable|string|max:255',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'pincode'         => 'required|string|max:10',
            'address_type'    => 'required|in:home,work,other',
            'is_default'      => 'boolean'
        ]);

        // If setting as default, remove default from other addresses
        if ($request->is_default) {
            ShippingAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        // Create new address for the logged-in user
        $address = Auth::user()->shippingAddresses()->create($request->all());

        // Redirect based on previous page
        $previousUrl = url()->previous();
        if (str_contains($previousUrl, 'checkout')) {
            return redirect()->route('orders.checkout')
                            ->with('success', 'Address added successfully!');
        }

        return redirect()->route('addresses.index')
                        ->with('success', 'Address added successfully!');
    }


    public function edit(ShippingAddress $address)
    {
        if ($address->user_id != Auth::id()) {
            abort(403);
        }
        return view('addresses.edit', compact('address'));
    }

    public function update(Request $request, ShippingAddress $address)
    {
        if ($address->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'address_type' => 'required|in:home,work,other',
            'is_default' => 'boolean'
        ]);

        if ($request->is_default) {
            ShippingAddress::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($request->all());

        return redirect()->route('addresses.index')->with('success', 'Address updated successfully!');
    }

    public function destroy(ShippingAddress $address)
    {
        if ($address->user_id != Auth::id()) {
            abort(403);
        }

        if ($address->is_default) {
            return back()->with('error', 'Cannot delete default address. Set another address as default first.');
        }

        $address->delete();

        return redirect()->route('addresses.index')->with('success', 'Address deleted successfully!');
    }

    public function setDefault(ShippingAddress $address)
    {
        if ($address->user_id != Auth::id()) {
            abort(403);
        }

        ShippingAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated!');
    }
}