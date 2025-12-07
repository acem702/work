@extends('layouts.user')

@section('title', $page->title)

@section('content')
<div class="space-y-6">
    
    <!-- Page Title with Back Button -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">{{ $page->title }}</h1>
        
        <a href="{{ route('dashboard') }}" 
           class="flex items-center space-x-2 px-4 py-2 bg-white border border-gray-300 rounded-xl text-gray-700 text-sm font-semibold hover:bg-gray-50 hover:border-orange-500 transition group">
            <i class="fas fa-arrow-left text-gray-500 group-hover:text-orange-500 transition"></i>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <!-- Orange Divider Line -->
    <div class="w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

    <!-- Page Content -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <article class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </article>
    </div>

</div>
@endsection