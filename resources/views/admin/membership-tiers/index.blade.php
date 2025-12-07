@extends('layouts.admin')

@section('title', 'Membership Tiers')

@section('content')
<div class="p-6" x-data="membershipTiersManager()">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Membership Tiers</h1>
            <p class="text-gray-600 mt-1">Manage membership levels and benefits</p>
        </div>
        <button @click="openCreateModal()" 
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
            <i class="fas fa-plus mr-2"></i>
            Create New Tier
        </button>
    </div>

    <!-- Tiers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($tiers as $tier)
            <div class="bg-white rounded-xl shadow-sm border-2 {{ $tier->level === 5 ? 'border-purple-500' : 'border-gray-200' }} overflow-hidden hover:shadow-md transition">
                
                <!-- Tier Header with Image -->
                <div class="bg-gradient-to-br from-primary to-secondary p-6 text-white">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            @if($tier->image_url)
                                <img src="{{ Storage::url($tier->image_url) }}" 
                                     alt="{{ $tier->name }}" 
                                     class="w-16 h-16 object-contain rounded-lg bg-white/20 p-2">
                            @else
                                <div class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-3xl text-white/50"></i>
                                </div>
                            @endif
                            <h3 class="text-2xl font-bold">{{ $tier->name }}</h3>
                        </div>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
                            Level {{ $tier->level }}
                        </span>
                    </div>
                    @if($tier->description)
                        <p class="text-white/90 text-sm">{{ $tier->description }}</p>
                    @endif
                </div>

                <!-- Tier Details -->
                <div class="p-6 space-y-4">
                    
                    <!-- User Count -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <span class="text-gray-600">Active Users</span>
                        <span class="text-2xl font-bold text-gray-900">{{ $tier->users_count }}</span>
                    </div>

                    <!-- Benefits List -->
                    <div class="space-y-3">
                        <!-- Daily Task Limit -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-tasks text-blue-500 w-5 mr-3"></i>
                                <span class="text-sm">Daily Task Limit</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $tier->daily_task_limit }}</span>
                        </div>

                        <!-- Commission Multiplier -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-chart-line text-green-500 w-5 mr-3"></i>
                                <span class="text-sm">Commission Multiplier</span>
                            </div>
                            <span class="font-semibold text-green-600">×{{ $tier->commission_multiplier }}</span>
                        </div>

                        <!-- Upgrade Cost -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-coins text-yellow-500 w-5 mr-3"></i>
                                <span class="text-sm">Upgrade Cost</span>
                            </div>
                            <span class="font-semibold text-gray-900">
                                @if($tier->upgrade_cost > 0)
                                    {{ number_format($tier->upgrade_cost) }} pts
                                @else
                                    <span class="text-green-600">Free</span>
                                @endif
                            </span>
                        </div>

                        <!-- Products Available -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-box text-purple-500 w-5 mr-3"></i>
                                <span class="text-sm">Products Available</span>
                            </div>
                            <span class="font-semibold text-gray-900">
                                {{ \App\Models\Product::whereHas('minMembershipTier', fn($q) => $q->where('level', '<=', $tier->level))->count() }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-toggle-on text-gray-500 w-5 mr-3"></i>
                                <span class="text-sm">Status</span>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $tier->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $tier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Commission Examples -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs font-semibold text-gray-600 mb-2">Commission Examples:</p>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="bg-gray-50 rounded p-2">
                                <p class="text-gray-600">Base: 10</p>
                                <p class="font-bold text-gray-900">{{ 10 * $tier->commission_multiplier }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-2">
                                <p class="text-gray-600">Base: 50</p>
                                <p class="font-bold text-gray-900">{{ 50 * $tier->commission_multiplier }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-2">
                                <p class="text-gray-600">Base: 100</p>
                                <p class="font-bold text-gray-900">{{ 100 * $tier->commission_multiplier }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3 pt-4">
                        <button @click="openEditModal({{ $tier->toJson() }})" 
                                class="px-4 py-2 bg-blue-500 text-white text-center rounded-lg hover:bg-blue-600 transition text-sm">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </button>
                        <button @click="toggleStatus({{ $tier->id }})" 
                                class="px-4 py-2 {{ $tier->is_active ? 'bg-gray-500' : 'bg-green-500' }} text-white text-center rounded-lg hover:opacity-90 transition text-sm">
                            <i class="fas {{ $tier->is_active ? 'fa-pause' : 'fa-play' }} mr-1"></i>
                            {{ $tier->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </div>

                    <button onclick="deleteTier({{ $tier->id }}, '{{ $tier->name }}')" 
                            class="w-full px-4 py-2 bg-red-500 text-white text-center rounded-lg hover:bg-red-600 transition text-sm">
                        <i class="fas fa-trash mr-1"></i>
                        Delete Tier
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="closeModal()">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900" x-text="isEditing ? 'Edit Membership Tier' : 'Create New Membership Tier'"></h3>
            </div>

            <form @submit.prevent="submitForm" class="p-6 space-y-6" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tier Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tier Name *</label>
                        <input type="text" 
                               x-model="form.name"
                               required
                               placeholder="e.g., Gold Member"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <!-- Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                        <input type="number" 
                               x-model="form.level"
                               required
                               min="1"
                               placeholder="1, 2, 3..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Higher level = better benefits</p>
                    </div>

                    <!-- Daily Task Limit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Daily Task Limit *</label>
                        <input type="number" 
                               x-model="form.daily_task_limit"
                               required
                               min="1"
                               placeholder="e.g., 30"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <!-- Commission Multiplier -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Commission Multiplier *</label>
                        <input type="number" 
                               x-model="form.commission_multiplier"
                               required
                               min="0.1"
                               step="0.1"
                               placeholder="e.g., 1.5"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Base commission × this value</p>
                    </div>

                    <!-- Upgrade Cost -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upgrade Cost (USD) *</label>
                        <input type="number" 
                               x-model="form.upgrade_cost"
                               required
                               min="0"
                               step="0.01"
                               placeholder="e.g., 100.00"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <!-- Tier Image Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tier Image</label>
                        
                        <!-- Current Image Preview -->
                        <div x-show="currentImageUrl || imagePreview" class="mb-3">
                            <div class="relative inline-block">
                                <img :src="imagePreview || currentImageUrl" 
                                     alt="Tier image preview"
                                     class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200">
                                <button type="button"
                                        @click="removeImage()"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 transition flex items-center justify-center">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- File Input -->
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF, SVG, WEBP (MAX. 2MB)</p>
                                </div>
                                <input type="file" 
                                       class="hidden" 
                                       accept="image/*"
                                       @change="handleImageUpload($event)">
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea x-model="form.description"
                                  rows="3"
                                  placeholder="Brief description of this tier..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" 
                            @click="closeModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition">
                        <i class="fas fa-check mr-2"></i>
                        <span x-text="isEditing ? 'Update Tier' : 'Create Tier'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function membershipTiersManager() {
        return {
            showModal: false,
            isEditing: false,
            editingId: null,
            imageFile: null,
            imagePreview: null,
            currentImageUrl: null,
            removeImageFlag: false,
            form: {
                name: '',
                level: '',
                daily_task_limit: '',
                commission_multiplier: '',
                upgrade_cost: '',
                description: ''
            },

            openCreateModal() {
                this.isEditing = false;
                this.editingId = null;
                this.resetForm();
                this.showModal = true;
            },

            openEditModal(tier) {
                this.isEditing = true;
                this.editingId = tier.id;
                this.form = {
                    name: tier.name,
                    level: tier.level,
                    daily_task_limit: tier.daily_task_limit,
                    commission_multiplier: tier.commission_multiplier,
                    upgrade_cost: tier.upgrade_cost,
                    description: tier.description || ''
                };
                
                // Set current image URL if exists
                if (tier.image_url) {
                    this.currentImageUrl = '{{ asset("storage") }}/' + tier.image_url;
                }
                
                this.imagePreview = null;
                this.imageFile = null;
                this.removeImageFlag = false;
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
                this.resetForm();
            },

            resetForm() {
                this.form = {
                    name: '',
                    level: '',
                    daily_task_limit: '',
                    commission_multiplier: '',
                    upgrade_cost: '',
                    description: ''
                };
                this.imageFile = null;
                this.imagePreview = null;
                this.currentImageUrl = null;
                this.removeImageFlag = false;
            },

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imageFile = file;
                    
                    // Create preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeImage() {
                this.imageFile = null;
                this.imagePreview = null;
                this.currentImageUrl = null;
                this.removeImageFlag = true;
            },

            async submitForm() {
                try {
                    const formData = new FormData();
                    
                    // Append form fields
                    Object.keys(this.form).forEach(key => {
                        formData.append(key, this.form[key]);
                    });

                    // Append image if uploaded
                    if (this.imageFile) {
                        formData.append('image', this.imageFile);
                    }

                    // Flag to remove image
                    if (this.removeImageFlag) {
                        formData.append('remove_image', '1');
                    }

                    const url = this.isEditing 
                        ? `/admin/membership-tiers/${this.editingId}`
                        : '{{ route("admin.membership-tiers.store") }}';
                    
                    // Add _method for PUT request
                    if (this.isEditing) {
                        formData.append('_method', 'PUT');
                    }

                    const response = await axios.post(url, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response.data.success) {
                        alert(response.data.message);
                        location.reload();
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Error saving tier');
                }
            },

            async toggleStatus(tierId) {
                if (!confirm('Are you sure you want to change the status of this tier?')) return;

                try {
                    const response = await axios.post(`/admin/membership-tiers/${tierId}/toggle-status`);
                    
                    if (response.data.success) {
                        alert(response.data.message);
                        location.reload();
                    }
                } catch (error) {
                    alert(error.response?.data?.message || 'Error updating status');
                }
            }
        }
    }

    async function deleteTier(tierId, tierName) {
        if (!confirm(`Are you sure you want to delete "${tierName}"? This cannot be undone.`)) return;

        try {
            const response = await axios.delete(`/admin/membership-tiers/${tierId}`);
            
            if (response.data.success) {
                alert(response.data.message);
                location.reload();
            }
        } catch (error) {
            alert(error.response?.data?.message || 'Error deleting tier');
        }
    }
</script>
@endpush