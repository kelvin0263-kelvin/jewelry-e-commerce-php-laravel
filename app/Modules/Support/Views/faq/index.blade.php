@extends('layouts.app')

@section('title', 'FAQ - Jewelry Store')

@section('content')



    <div class="py-8">
        <div >
            <!-- FAQ Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-8 text-center">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Find answers to common questions about our jewelry, ordering process, shipping, and more. 
                        If you can't find what you're looking for, feel free to contact our customer service team.
                    </p>
                </div>
            </div>

            <!-- FAQ Categories -->
            <div class="space-y-8">
                @foreach($faqs as $categoryKey => $category)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Category Title -->
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b border-gray-200 pb-3">
                                {{ $category['title'] }}
                            </h2>
                            
                            <!-- Questions in this category -->
                            <div class="space-y-4">
                                @foreach($category['questions'] as $index => $faq)
                                    <div class="border border-gray-200 rounded-lg">
                                        <button 
                                            type="button" 
                                            class="w-full text-left p-4 focus:outline-none focus:ring-2 focus:ring-blue-500 hover:bg-gray-50 transition duration-150"
                                            onclick="toggleFaq('{{ $categoryKey }}_{{ $index }}')"
                                        >
                                            <div class="flex justify-between items-center">
                                                <h3 class="text-lg font-semibold text-gray-900 pr-4">
                                                    {{ $faq['question'] }}
                                                </h3>
                                                <svg 
                                                    id="icon_{{ $categoryKey }}_{{ $index }}" 
                                                    class="w-5 h-5 text-gray-600 transform transition-transform duration-200" 
                                                    fill="none" 
                                                    stroke="currentColor" 
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </button>
                                        <div 
                                            id="answer_{{ $categoryKey }}_{{ $index }}" 
                                            class="hidden px-4 pb-4"
                                        >
                                            <div class="text-gray-700 leading-relaxed pt-2 border-t border-gray-100">
                                                {{ $faq['answer'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Contact Section -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white overflow-hidden shadow-sm sm:rounded-lg mt-12">
                <div class="p-8 text-center">
                    <h2 class="text-2xl font-bold mb-4">Still have questions?</h2>
                    <p class="text-lg mb-6 opacity-90">
                        Our customer service team is here to help you with any questions or concerns.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a 
                            href="mailto:support@jewelrystore.com" 
                            class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-150"
                        >
                            Email Support
                        </a>
                        <a 
                            href="tel:1-800-JEWELRY" 
                            class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-150"
                        >
                            Call 1-800-JEWELRY
                        </a>
                        @if(Route::has('chat.conversations'))
                            <a 
                                href="{{ route('chat.conversations') }}" 
                                class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition duration-150"
                            >
                                Live Chat
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Search FAQ Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Search FAQ</h3>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="faqSearch" 
                            placeholder="Search for answers..." 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onkeyup="searchFAQ()"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFaq(id) {
            const answer = document.getElementById('answer_' + id);
            const icon = document.getElementById('icon_' + id);
            
            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                answer.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function searchFAQ() {
            const searchTerm = document.getElementById('faqSearch').value.toLowerCase();
            const faqItems = document.querySelectorAll('[id^="answer_"]');
            
            faqItems.forEach(item => {
                const questionButton = item.previousElementSibling;
                const questionText = questionButton.textContent.toLowerCase();
                const answerText = item.textContent.toLowerCase();
                const faqContainer = item.closest('.border');
                
                if (questionText.includes(searchTerm) || answerText.includes(searchTerm) || searchTerm === '') {
                    faqContainer.style.display = 'block';
                    
                    // Highlight matching text if search term exists
                    if (searchTerm && searchTerm.length > 2) {
                        if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                            // Show the answer for matching items
                            item.classList.remove('hidden');
                            const icon = questionButton.querySelector('svg');
                            icon.style.transform = 'rotate(180deg)';
                        }
                    }
                } else {
                    faqContainer.style.display = 'none';
                }
            });
        }

        // Auto-expand first FAQ in each category on page load
        document.addEventListener('DOMContentLoaded', function() {
            const categories = @json(array_keys($faqs));
            categories.forEach(category => {
                toggleFaq(category + '_0');
            });
        });
    </script>
@endsection
