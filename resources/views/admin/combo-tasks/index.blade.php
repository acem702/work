@extends('layouts.admin')

@section('title', 'Combo Tasks Management')

@section('content')
<div class="p-6" x-data="comboTaskManager()">
    
    <!-- Page Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Combo Tasks</h1>
            <p class="text-gray-600 mt-1">Create and manage multi-step combo tasks</p>
        </div>
        <button @click="showCreateModal = true" 
                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-plus mr-2"></i>
            Create Combo Task
        </button>
    </div>

    <!-- Combo Tasks List -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($comboTasks as $combo)
            <div class="bg-white rounded-xl shadow-sm border-2 border-purple-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-bold text-gray-900">{{ $combo->name }}</h3>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">
                                    {{ $combo->sequence_count }} Tasks
                                </span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $combo->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $combo->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            @if($combo->description)
                                <p class="text-gray-600 text-sm">{{ $combo->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="toggleStatus({{ $combo->id }}, {{ $combo->is_active ? 'false' : 'true' }})" 
                                    class="px-3 py-2 text-sm {{ $combo->is_active ? 'bg-gray-100 text-gray-700' : 'bg-green-100 text-green-700' }} rounded-lg hover:opacity-80 transition">
                                <i class="fas {{ $combo->is_active ? 'fa-pause' : 'fa-play' }} mr-1"></i>
                                {{ $combo->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button onclick="deleteComboTask({{ $combo->id }})" 
                                    class="px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                <i class="fas fa-trash mr-1"></i>
                                Delete
                            </button>
                        </div>
                    </div>

                    <!-- Task Sequence -->
                    <div class="mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Task Sequence:</h4>
                        <div class="flex items-center gap-2 overflow-x-auto pb-2">
                            @foreach($combo->items as $index => $item)
                                <div class="flex items-center flex-shrink-0">
                                    <!-- Task Card -->
                                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-300 rounded-lg p-3 min-w-[200px]">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                                {{ $item->sequence_order }}
                                            </span>
                                            <span class="text-xs font-semibold text-purple-700">Step {{ $item->sequence_order }}</span>
                                        </div>
                                        <h5 class="font-semibold text-gray-900 text-sm mb-1">{{ $item->product->name }}</h5>
                                        <div class="flex items-center justify-between text-xs text-gray-600">
                                            <span class="flex items-center">
                                                <i class="fas fa-coins text-purple-600 mr-1"></i>
                                                {{ number_format($item->product->base_points) }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-gift text-green-600 mr-1"></i>
                                                {{ number_format($item->product->base_commission) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Arrow -->
                                    @if(!$loop->last)
                                        <div class="flex items-center px-2">
                                            <i class="fas fa-arrow-right text-purple-400 text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Summary Stats -->
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-xs text-gray-600 mb-1">Total Points Required</p>
                            <p class="text-lg font-bold text-blue-600">{{ number_format($combo->total_base_points) }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="text-xs text-gray-600 mb-1">Total Commission</p>
                            <p class="text-lg font-bold text-green-600">
                                {{ number_format($combo->items->sum(fn($i) => $i->product->base_commission)) }}
                            </p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3">
                            <p class="text-xs text-gray-600 mb-1">Assigned Users</p>
                            <p class="text-lg font-bold text-purple-600">
                                {{ $combo->taskQueues()->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="fas fa-layer-group text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-2">No combo tasks created yet</p>
                <p class="text-gray-400 text-sm mb-4">Create your first combo task to get started</p>
                <button @click="showCreateModal = true" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-plus mr-2"></i>
                    Create Combo Task
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($comboTasks->hasPages())
        <div class="mt-6">
            {{ $comboTasks->links() }}
        </div>
    @endif

    <!-- Create Combo Task Modal -->
    <div x-show="showCreateModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showCreateModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900">Create Combo Task</h3>
                <p class="text-gray-600 text-sm mt-1">Combine multiple products into a sequential task</p>
            </div>

            <form @submit.prevent="createComboTask" class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Combo Name</label>
                    <input type="text" 
                           x-model="comboForm.name"
                           required
                           placeholder="e.g., Premium Shopping Combo"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea x-model="comboForm.description"
                              rows="2"
                              placeholder="Describe this combo task..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Products (Sequence Order)
                        <span class="text-gray-500 font-normal">- Click to add in order</span>
                    </label>
                    
                    <!-- Selected Products (in order) -->
                    <div class="mb-4 p-4 bg-purple-50 border-2 border-purple-200 rounded-lg min-h-[100px]">
                        <p class="text-xs font-semibold text-purple-700 mb-2">Selected Sequence:</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(productId, index) in comboForm.product_ids" :key="index">
                                <div class="flex items-center bg-white border-2 border-purple-300 rounded-lg px-3 py-2">
                                    <span class="w-5 h-5 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-2"
                                          x-text="index + 1"></span>
                                    <span class="text-sm font-medium" x-text="getProductName(productId)"></span>
                                    <button type="button" 
                                            @click="comboForm.product_ids.splice(index, 1)"
                                            class="ml-2 text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                            <div x-show="comboForm.product_ids.length === 0" class="text-gray-400 text-sm italic">
                                No products selected yet...
                            </div>
                        </div>
                    </div>

                    <!-- Available Products -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @foreach($products as $product)
                            <button type="button"
                                    @click="addProductToCombo({{ $product->id }})"
                                    class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 transition text-left">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Points: {{ number_format($product->base_points) }} | 
                                        Commission: {{ number_format($product->base_commission) }}
                                    </p>
                                </div>
                                <i class="fas fa-plus text-purple-600"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" 
                            @click="showCreateModal = false; resetForm()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="comboForm.product_ids.length < 2"
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check mr-2"></i>
                        Create Combo Task
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const products = @json($products);

    function comboTaskManager() {
        return {
            showCreateModal: false,
            comboForm: {
                name: '',
                description: '',
                product_ids: []
            },

            addProductToCombo(productId) {
                if (!this.comboForm.product_ids.includes(productId)) {
                    this.comboForm.product_ids.push(productId);
                }
            },

            getProductName(productId) {
                const product = products.find(p => p.id === productId);
                return product ? product.name : 'Unknown';
            },

            async createComboTask() {
                if (this.comboForm.product_ids.length < 2) {
                    alert('Please select at least 2 products');
                    return;
                }

                try {
                    const response = await axios.post('{{ route("admin.combo-tasks.store") }}', this.comboForm);

                    if (response.data.success) {
                        alert(response.data.message);
                        location.reload();
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Error creating combo task');
                }
            },

            async toggleStatus(comboId, newStatus) {
                try {
                    const response = await axios.post(`/admin/combo-tasks/${comboId}/toggle-status`);
                    
                    if (response.data.success) {
                        location.reload();
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Error updating status');
                }
            },

            resetForm() {
                this.comboForm = {
                    name: '',
                    description: '',
                    product_ids: []
                };
            }
        }
    }

    async function deleteComboTask(comboId) {
        if (!confirm('Are you sure you want to delete this combo task?')) return;

        try {
            const response = await axios.delete(`/admin/combo-tasks/${comboId}`);
            
            if (response.data.success) {
                alert(response.data.message);
                location.reload();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Error deleting combo task');
        }
    }
</script>
@endpush