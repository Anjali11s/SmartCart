<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\BudgetController;

class CartController extends Controller
{
    /**
     * All cart methods need authentication
     * Because only logged-in users can have a cart
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * DISPLAY CART PAGE
     * GET /cart
     * 
     * What it does: Shows all items in user's cart
     */
    public function index()
    {
        // 1. Get the currently logged-in user
        $user = Auth::user();
        
        // 2️. Get user's cart (or create if doesn't exist)
        $cart = $this->getUserCart($user);
        
        // 3️. Get all items in this cart with product details
        $cartItems = $cart->items()->with('product')->get();
        
        // 4️. Calculate cart total
        $total = $this->calculateTotal($cartItems);
        
        // 5️. Get user's budget for budget tracker (connects to YOUR work!)
        $budget = $user->budget;
        
        // 6️. Return the cart view with data
        return view('cart.index', compact('cartItems', 'total', 'budget'));
    }

    /**
     * ADD PRODUCT TO CART
     * POST /cart/add
     * 
     * @param Request $request - contains product_id and quantity
     */
    /**
 * ADD PRODUCT TO CART
 * POST /cart/add
 */
    public function add(Request $request)
    {
        // 1. Validate the incoming data
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // 2. Get the product from database
        $product = Product::findOrFail($request->product_id);
        
        // 3. Check if enough stock is available
        if ($product->quantity < $request->quantity) {
            return back()->with('error', "Only {$product->quantity} items available for '{$product->name}'");
        }

        // 4. Get user's cart
        $user = Auth::user();
        $cart = $this->getUserCart($user);

        //  Calculate NEW cart total after adding this item
        $currentCartItems = $cart->items()->with('product')->get();
        $currentCartTotal = $this->calculateTotal($currentCartItems);
        $newCartTotal = $currentCartTotal + ($product->price * $request->quantity);

        $budgetController = new BudgetController();
        $budgetCheck = $budgetController->checkCartAgainstBudget($newCartTotal);  // ✅ CORRECT

        if (!$budgetCheck['allowed']) {
            return back()->with('error', $budgetCheck['error']);
        }
        
        // 5. Check if product already exists in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $product->id)
                            ->first();
        
        if ($cartItem) {
            // If exists, UPDATE quantity
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            // Check stock again for new total
            if ($product->quantity < $newQuantity) {
                return back()->with('error', "Cannot add {$request->quantity} more. Only {$product->quantity} total available.");
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // If not exists, CREATE new cart item
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', "{$product->name} added to cart!");
    }

    /**
     * UPDATE CART ITEM QUANTITY
     * PATCH /cart/update/{cartItemId}
     */
    public function update(Request $request, $cartItemId)
    {
        // 1️. Validate the new quantity
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        // 2️. Find the cart item (belongs to user's cart only)
        $cartItem = CartItem::findOrFail($cartItemId);
        
        // 3️. SECURITY: Verify this cart item belongs to logged-in user
        $user = Auth::user();
        $cart = $this->getUserCart($user);
        
        if ($cartItem->cart_id !== $cart->id) {
            abort(403, 'Unauthorized action');  // Prevent editing others' carts
        }
        
        // 4️. Get the product and check stock
        $product = $cartItem->product;
        
        if ($product->quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Only {$product->quantity} items available"
            ], 400);
        }
        
        // 5️. Update quantity
        $cartItem->update(['quantity' => $request->quantity]);
        
        // 6️. Recalculate total for UI update
        $cartItems = $cart->items()->with('product')->get();
        $newTotal = $this->calculateTotal($cartItems);
        
        // For AJAX requests, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'item_total' => $product->price * $request->quantity,
                'cart_total' => $newTotal,
                'message' => 'Quantity updated'
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', 'Cart updated!');
    }

    /**
     * REMOVE ITEM FROM CART
     * DELETE /cart/remove/{cartItemId}
     */
    public function remove($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        
        // Security check
        $user = Auth::user();
        $cart = $this->getUserCart($user);
        
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }
        
        $productName = $cartItem->product->name;
        $cartItem->delete();
        
        return redirect()->route('cart.index')->with('success', "'{$productName}' removed from cart");
    }

    /**
     * LEAR ENTIRE CART
     * DELETE /cart/clear
     */
    public function clear()
    {
        $user = Auth::user();
        $cart = $this->getUserCart($user);
        
        $cart->items()->delete();  // Delete all cart items
        
        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }

    // HELPER METHODS 
    
    /**
     * Get or create user's cart
     * This ensures every user ALWAYS has a cart
     */
    private function getUserCart($user)
    {
        // First, check if user already has a cart
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            // If no cart exists, create one
            $cart = Cart::create([
                'user_id' => $user->id
            ]);
        }
        
        return $cart;
    }
    
    /**
     * Calculate total price of all items in cart
     */
    private function calculateTotal($cartItems)
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }
        return $total;
    }
}