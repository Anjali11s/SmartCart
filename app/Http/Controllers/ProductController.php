<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('seller')->latest()->paginate(12);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null;

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $imagePath,
            'seller_id' => Auth::id(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created!');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorizeSeller($product);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeSeller($product);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update($request->only(['name', 'price', 'quantity']));
        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeSeller($product);
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    /**
     * Authorize that current user is seller of this product or admin.
     */
    private function authorizeSeller(Product $product)
    {
        if ($product->seller_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}