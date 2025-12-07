<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComboTask;
use App\Models\ComboTaskItem;
use App\Models\Product;
use App\Models\MembershipTier;
use App\Services\TaskQueueService;
use Illuminate\Http\Request;
use DB;
use Exception;

class ComboTaskController extends Controller
{
    protected $taskQueueService;

    public function __construct(TaskQueueService $taskQueueService)
    {
        $this->middleware('admin');
        $this->taskQueueService = $taskQueueService;
    }

    /**
     * Display combo tasks management page
     */
    public function index()
    {
        $comboTasks = ComboTask::with(['items.product.minMembershipTier'])
            ->latest()
            ->paginate(10);

        $products = Product::active()->with('minMembershipTier')->get();

        return view('admin.combo-tasks.index', compact('comboTasks', 'products'));
    }

    /**
     * Store a new combo task
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_ids' => 'required|array|min:2',
            'product_ids.*' => 'required|exists:products,id',
        ]);

        try {
            $comboTask = DB::transaction(function () use ($request) {
                // Create combo task
                $comboTask = ComboTask::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'sequence_count' => count($request->product_ids),
                    'is_active' => true,
                ]);

                // Create combo task items
                $totalPoints = 0;
                foreach ($request->product_ids as $index => $productId) {
                    $product = Product::findOrFail($productId);
                    $totalPoints += $product->base_points;

                    ComboTaskItem::create([
                        'combo_task_id' => $comboTask->id,
                        'product_id' => $productId,
                        'sequence_order' => $index + 1,
                    ]);
                }

                // Update total points
                $comboTask->update(['total_base_points' => $totalPoints]);

                return $comboTask;
            });

            return response()->json([
                'success' => true,
                'message' => 'Combo task created successfully',
                'combo_task' => $comboTask->load('items.product'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update combo task
     */
    public function update(Request $request, ComboTask $comboTask)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $comboTask->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? $comboTask->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Combo task updated successfully',
                'combo_task' => $comboTask->fresh(['items.product']),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete combo task
     */
    public function destroy(ComboTask $comboTask)
    {
        try {
            // Check if combo task is in use
            if ($comboTask->taskQueues()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete combo task that is assigned to users',
                ], 400);
            }

            $comboTask->delete();

            return response()->json([
                'success' => true,
                'message' => 'Combo task deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Toggle combo task status
     */
    public function toggleStatus(ComboTask $comboTask)
    {
        try {
            $comboTask->update(['is_active' => !$comboTask->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'is_active' => $comboTask->is_active,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get combo task details
     */
    public function show(ComboTask $comboTask)
    {
        return response()->json([
            'success' => true,
            'combo_task' => $comboTask->load('items.product.minMembershipTier'),
        ]);
    }
}