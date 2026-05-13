<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BudgetExceededMail;

class BudgetController extends Controller
{
    /**
     * All budget methods require authentication
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * DISPLAY BUDGET PAGE
     * GET /budget
     * 
     * Shows budget settings and current status
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's budget (or null if not set)
        $budget = Budget::where('user_id', $user->id)->first();
        
        // Get current cart total
        $cartTotal = $this->getCartTotal($user);
        
        // Calculate budget status
        $budgetStatus = $this->calculateBudgetStatus($budget, $cartTotal);
        
        return view('budget.index', compact('budget', 'cartTotal', 'budgetStatus'));
    }

    /**
     * SAVE OR UPDATE BUDGET
     * POST /budget
     * 
     * @param Request $request - contains amount
     */
    public function store(Request $request)
    {
        // 1️. Validate the budget amount
        $request->validate([
            'amount' => 'required|numeric|min:0|max:9999999.99',
        ], [
            'amount.required' => 'Please enter a budget amount.',
            'amount.numeric' => 'Budget must be a number.',
            'amount.min' => 'Budget cannot be negative.',
            'amount.max' => 'Budget amount is too high.',
        ]);

        $user = Auth::user();
        
        // 2️. Update or create budget
        $budget = Budget::updateOrCreate(
            ['user_id' => $user->id],
            ['amount' => $request->amount]
        );
        
        // 3️. Check if cart total exceeds new budget
        $cartTotal = $this->getCartTotal($user);
        
        if ($cartTotal > $request->amount) {
            // Budget exceeded alert
            session()->flash('warning', "⚠️ Your cart total (₹" . number_format($cartTotal) . 
                            ") exceeds your new budget! Please review your cart.");
            
            // Send email notification with null check
            $this->sendBudgetAlert($user, $cartTotal, $request->amount, 'exceeded');
        } elseif ($cartTotal > ($request->amount * 0.7)) {
            // Near budget limit warning
            session()->flash('info', "📊 Note: Your cart total (₹" . number_format($cartTotal) . 
                            ") is close to your budget limit.");
        }
        
        return redirect()->route('budget.index')
                        ->with('success', 'Budget updated successfully!');
    }

    /**
     * GET BUDGET STATUS (AJAX endpoint for real-time updates)
     * GET /budget/status
     */
    public function getStatus()
    {
        $user = Auth::user();
        $budget = Budget::where('user_id', $user->id)->first();
        $cartTotal = $this->getCartTotal($user);
        
        $status = $this->calculateBudgetStatus($budget, $cartTotal);
        
        return response()->json([
            'success' => true,
            'has_budget' => !is_null($budget),
            'budget_amount' => $budget ? number_format($budget->amount, 2) : 0,
            'cart_total' => number_format($cartTotal, 2),
            'remaining' => $status['remaining'],
            'percentage' => $status['percentage'],
            'alert_level' => $status['alert_level'],
            'alert_message' => $status['alert_message'],
            'is_exceeded' => $status['is_exceeded']
        ]);
    }

    /**
     * CHECK BEFORE CHECKOUT (Called by checkout system)
     */
    public function checkCartAgainstBudget($cartTotal)
    {
        $user = Auth::user();
        $budget = Budget::where('user_id', $user->id)->first();
        
        if (!$budget) {
            return [
                'allowed' => true,
                'warning' => "You haven't set a monthly budget. Consider setting one to track your spending!"
            ];
        }
        
        if ($cartTotal > $budget->amount) {
            return [
                'allowed' => false,
                'error' => "❌ Cannot add item! Your cart total would be ₹" . number_format($cartTotal) . 
                        " which exceeds your budget (₹" . number_format($budget->amount) . 
                        "). Please remove items or increase your budget."
            ];
        }
        
        return [
            'allowed' => true,
            'remaining' => $budget->amount - $cartTotal
        ];
    }
    
    public function canCheckout()
    {
        $user = Auth::user();
        $budget = Budget::where('user_id', $user->id)->first();
        $cartTotal = $this->getCartTotal($user);
        
        if (!$budget) {
            return [
                'allowed' => true,
                'warning' => "You haven't set a monthly budget. Consider setting one to track your spending!"
            ];
        }
        
        // Include monthly spent
        $monthlySpent = $user->orders()
            ->where('order_status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        
        $remainingBudget = $budget->amount - $monthlySpent;
        
        if ($cartTotal > $remainingBudget) {
            return [
                'allowed' => false,
                'error' => "❌ Cannot checkout! Your cart total (₹" . number_format($cartTotal) . 
                        ") exceeds your remaining budget (₹" . number_format($remainingBudget) . 
                        "). You have already spent ₹" . number_format($monthlySpent) . " this month."
            ];
        }
        
        return [
            'allowed' => true,
            'info' => "✅ Within budget! Remaining: ₹" . number_format($remainingBudget - $cartTotal)
        ];
    }

    /**
     * GET BUDGET INSIGHTS (Analytics)
     * GET /budget/insights
     */
    public function insights()
    {
        $user = Auth::user();
        $budget = Budget::where('user_id', $user->id)->first();
        
        if (!$budget) {
            return redirect()->route('budget.index')
                           ->with('info', 'Please set a budget first to see insights.');
        }
        
        $orders = $user->orders()
                      ->where('order_status', 'delivered')
                      ->orderBy('created_at', 'desc')
                      ->take(10)
                      ->get();
        
        $totalSpent = $orders->sum('total_amount');
        $averageSpent = $orders->count() > 0 ? $totalSpent / $orders->count() : 0;
        
        $monthlySpending = $user->orders()
                               ->where('order_status', 'delivered')
                               ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total')
                               ->groupBy('month')
                               ->orderBy('month', 'desc')
                               ->take(6)
                               ->get();
        
        return view('budget.insights', compact('budget', 'orders', 'totalSpent', 'averageSpent', 'monthlySpending'));
    }

    // HELPER METHODS 

    private function getCartTotal($user)
    {
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return 0;
        }
        
        $cartItems = CartItem::where('cart_id', $cart->id)
                             ->with('product')
                             ->get();
        
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }
        
        return $total;
    }

    private function calculateBudgetStatus($budget, $cartTotal)
    {
        if (!$budget || $budget->amount == 0) {
            return [
                'has_budget' => false,
                'percentage' => 0,
                'remaining' => 'Not set',
                'alert_level' => 'no_budget',
                'alert_message' => 'No budget set. Click "Set Budget" to start tracking!',
                'is_exceeded' => false,
                'remaining_amount' => 0
            ];
        }
        
        $budgetAmount = $budget->amount;
        $percentage = ($cartTotal / $budgetAmount) * 100;
        $percentage = min(100, $percentage);
        $remaining = $budgetAmount - $cartTotal;
        
        if ($cartTotal > $budgetAmount) {
            $alertLevel = 'exceeded';
            $alertMessage = "🔴 BUDGET EXCEEDED! You've exceeded by ₹" . 
                           number_format($cartTotal - $budgetAmount);
        } elseif ($percentage >= 90) {
            $alertLevel = 'critical';
            $alertMessage = "🔴 CRITICAL! You've used " . number_format($percentage) . 
                           "% of your budget. Only ₹" . number_format($remaining) . " left!";
        } elseif ($percentage >= 70) {
            $alertLevel = 'warning';
            $alertMessage = "🟡 WARNING! You've used " . number_format($percentage) . 
                           "% of your budget. ₹" . number_format($remaining) . " remaining.";
        } elseif ($percentage >= 50) {
            $alertLevel = 'moderate';
            $alertMessage = "🟠 You've used " . number_format($percentage) . 
                           "% of your budget. ₹" . number_format($remaining) . " remaining.";
        } elseif ($percentage > 0) {
            $alertLevel = 'safe';
            $alertMessage = "🟢 Good! You've used " . number_format($percentage) . 
                           "% of your budget. ₹" . number_format($remaining) . " left.";
        } else {
            $alertLevel = 'empty';
            $alertMessage = "🟢 Your cart is empty! Start shopping!";
        }
        
        return [
            'has_budget' => true,
            'budget_amount' => $budgetAmount,
            'cart_total' => $cartTotal,
            'percentage' => round($percentage, 1),
            'remaining' => '₹' . number_format(max(0, $remaining)),
            'remaining_amount' => max(0, $remaining),
            'alert_level' => $alertLevel,
            'alert_message' => $alertMessage,
            'is_exceeded' => $cartTotal > $budgetAmount,
            'exceeded_by' => $cartTotal > $budgetAmount ? $cartTotal - $budgetAmount : 0
        ];
    }

    /**
     * Send email alert when budget is exceeded
     * Added null check to prevent mail sending errors
     */
    private function sendBudgetAlert($user, $cartTotal, $budgetAmount, $type)
    {
        // Log the alert
        \Log::info("Budget {$type} for user {$user->id}: Cart ₹{$cartTotal} / Budget ₹{$budgetAmount}");
        
        // Only send email if user has email and mail is configured
        if ($user->email && app()->environment('production')) {
            try {
                Mail::to($user->email)->send(new BudgetExceededMail($user, $cartTotal, $budgetAmount));
                \Log::info("Budget alert email sent to: {$user->email}");
            } catch (\Exception $e) {
                \Log::error("Failed to send budget alert email: " . $e->getMessage());
            }
        } elseif ($user->email && !app()->environment('production')) {
            // In development, log instead of sending
            \Log::info("Development mode: Would send budget alert email to {$user->email}");
        }
    }
}