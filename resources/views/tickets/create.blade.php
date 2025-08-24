@extends('layouts.app')

@section('title', 'Create Support Ticket')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800">Create Support Ticket</h1>
                <p class="text-gray-600 mt-2">Tell us about your issue and we'll help you resolve it quickly.</p>
            </div>
        </div>

        <!-- Ticket Form -->
        <div class="bg-white rounded-lg shadow-md">
            <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Subject -->
                    <div class="md:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject *
                        </label>
                        <input type="text" 
                               id="subject" 
                               name="subject" 
                               value="{{ old('subject') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Brief description of your issue"
                               required>
                        @error('subject')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category *
                        </label>
                        <select id="category" 
                                name="category" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <option value="">Select category...</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Priority *
                        </label>
                        <select id="priority" 
                                name="priority" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            @foreach($priorities as $key => $label)
                                <option value="{{ $key }}" {{ old('priority', 'normal') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description *
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="6"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Please provide detailed information about your issue..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">
                            Include steps to reproduce the issue, error messages, and any relevant details.
                        </p>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Email
                        </label>
                        <input type="email" 
                               id="contact_email" 
                               name="contact_email" 
                               value="{{ old('contact_email', auth()->user()->email) }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="your@email.com">
                        @error('contact_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Phone
                        </label>
                        <input type="tel" 
                               id="contact_phone" 
                               name="contact_phone" 
                               value="{{ old('contact_phone') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="+1 (555) 123-4567">
                        @error('contact_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preferred Contact Method -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Contact Method *
                        </label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="preferred_contact_method" 
                                       value="email" 
                                       {{ old('preferred_contact_method', 'email') === 'email' ? 'checked' : '' }}
                                       class="mr-2">
                                Email
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="preferred_contact_method" 
                                       value="phone" 
                                       {{ old('preferred_contact_method') === 'phone' ? 'checked' : '' }}
                                       class="mr-2">
                                Phone
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="preferred_contact_method" 
                                       value="portal" 
                                       {{ old('preferred_contact_method') === 'portal' ? 'checked' : '' }}
                                       class="mr-2">
                                Customer Portal
                            </label>
                        </div>
                        @error('preferred_contact_method')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Attachments -->
                    <div class="md:col-span-2">
                        <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                            Attachments
                        </label>
                        <input type="file" 
                               id="attachments" 
                               name="attachments[]" 
                               multiple
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('attachments.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">
                            Upload screenshots, documents, or other relevant files (max 5MB each, formats: jpg, png, pdf, doc, docx, txt)
                        </p>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('tickets.index') }}" 
                       class="text-gray-600 hover:text-gray-800">
                        ← Back to My Tickets
                    </a>
                    
                    <div class="flex gap-3">
                        <button type="button" 
                                onclick="window.history.back()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Ticket
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Text -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Need immediate help?</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>For urgent issues, you can also <a href="#" onclick="openChatWidget()" class="font-medium underline hover:text-blue-600">start a live chat</a> with our support team.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openChatWidget() {
    // Open the chat widget (assuming it exists)
    if (typeof window.openChatWidget === 'function') {
        window.openChatWidget();
    } else {
        alert('Chat widget not available. Please use the ticket system for support.');
    }
}
</script>
@endsection