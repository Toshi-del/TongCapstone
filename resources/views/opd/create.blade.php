@extends('layouts.opd')

@section('opd-content')
<div class="bg-gradient-to-b from-blue-50 to-white border border-blue-100 rounded-xl p-5 mb-6">
  <div class="flex items-center justify-between">
    <div class="flex items-center">
      <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
        <i class="fa-solid fa-stethoscope"></i>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-gray-900">Medical Test Catalog</h2>
        <p class="text-gray-500 text-sm">Browse available medical tests and services</p>
      </div>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('opd.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50 transition">
        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
      </a>
    </div>
  </div>
</div>

<!-- Search and Filter Bar -->
<div class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
  <div class="flex flex-col md:flex-row gap-4">
    <div class="flex-1">
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <i class="fa-solid fa-search text-gray-400"></i>
        </div>
        <input type="text" id="searchTests" placeholder="Search medical tests..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      </div>
    </div>
    <div class="flex gap-2">
      <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <option value="">All Categories</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
      <select id="priceFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <option value="">All Prices</option>
        <option value="0-500">₱0 - ₱500</option>
        <option value="500-1000">₱500 - ₱1,000</option>
        <option value="1000-2000">₱1,000 - ₱2,000</option>
        <option value="2000+">₱2,000+</option>
      </select>
    </div>
  </div>
</div>

<!-- Medical Test Categories -->
<div class="space-y-6">
  @foreach($categories as $category)
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden category-section" data-category-id="{{ $category->id }}">
      <!-- Category Header -->
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 p-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
              @switch($category->name)
                @case('Pre-Employment')
                  <i class="fa-solid fa-briefcase"></i>
                  @break
                @case('Appointment')
                  <i class="fa-solid fa-calendar-check"></i>
                  @break
                @case('Package')
                  <i class="fa-solid fa-box"></i>
                  @break
                @case('Routine Examinations')
                  <i class="fa-solid fa-clipboard-check"></i>
                  @break
                @case('Blood Chemistry')
                  <i class="fa-solid fa-vial"></i>
                  @break
                @case('X-RAY')
                  <i class="fa-solid fa-x-ray"></i>
                  @break
                @case('Serology')
                  <i class="fa-solid fa-microscope"></i>
                  @break
                @case('Thyroid Function Test')
                  <i class="fa-solid fa-heartbeat"></i>
                  @break
                @default
                  <i class="fa-solid fa-flask"></i>
              @endswitch
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
              <p class="text-sm text-gray-600">{{ $category->description }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ $category->medicalTests->count() }} tests
            </span>
            <button class="toggle-category text-gray-400 hover:text-gray-600 transition-colors" data-category="{{ $category->id }}">
              <i class="fa-solid fa-chevron-down"></i>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Tests Grid -->
      <div class="category-content p-4" id="category-{{ $category->id }}">
        @if($category->medicalTests->count() > 0)
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($category->medicalTests as $test)
              <div class="test-card border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200" 
                   data-test-name="{{ strtolower($test->name) }}" 
                   data-price="{{ $test->price }}"
                   data-category-id="{{ $category->id }}">
                <div class="flex items-start justify-between mb-3">
                  <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">{{ $test->name }}</h4>
                    @if($test->description)
                      <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $test->description }}</p>
                    @endif
                  </div>
                  <div class="ml-3 text-right">
                    <div class="text-lg font-bold text-blue-600">₱{{ number_format($test->price, 2) }}</div>
                  </div>
                </div>
                
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    @if($test->price <= 500)
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Basic
                      </span>
                    @elseif($test->price <= 1000)
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Standard
                      </span>
                    @else
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Premium
                      </span>
                    @endif
                  </div>
                  <button class="add-to-cart inline-flex items-center px-3 py-1 rounded-lg bg-blue-600 text-white text-xs hover:bg-blue-700 transition-colors"
                          data-test-id="{{ $test->id }}"
                          data-test-name="{{ $test->name }}"
                          data-test-price="{{ $test->price }}"
                          data-category-id="{{ $category->id }}"
                          data-category-name="{{ $category->name }}">
                    <i class="fa-solid fa-plus mr-1"></i> Add
                  </button>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-8 text-gray-500">
            <i class="fa-solid fa-flask text-3xl mb-2"></i>
            <p>No tests available in this category</p>
          </div>
        @endif
      </div>
    </div>
  @endforeach
</div>

<!-- Cart Summary (Fixed Bottom) -->
<div id="cartSummary" class="fixed bottom-4 right-4 bg-white border border-gray-200 rounded-xl shadow-lg p-4 min-w-80 hidden">
  <div class="flex items-center justify-between mb-3">
    <h4 class="font-semibold text-gray-900">Selected Tests</h4>
    <button id="clearCart" class="text-red-600 hover:text-red-700 text-sm">
      <i class="fa-solid fa-trash mr-1"></i> Clear
    </button>
  </div>
  <div id="cartItems" class="space-y-2 mb-3 max-h-40 overflow-y-auto">
    <!-- Cart items will be populated here -->
  </div>
  <div class="border-t pt-3">
    <div class="flex items-center justify-between mb-3">
      <span class="font-semibold">Total:</span>
      <span id="cartTotal" class="font-bold text-blue-600">₱0.00</span>
    </div>
    <button id="proceedToBooking" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
      <i class="fa-solid fa-calendar-plus mr-2"></i> Book Appointment
    </button>
  </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="bookingModalContent">
    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-t-2xl">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <i class="fa-solid fa-calendar-plus"></i>
          </div>
          <h3 class="text-xl font-bold">Book Appointment</h3>
        </div>
        <button id="closeBookingModal" class="text-white/80 hover:text-white transition-colors duration-200">
          <i class="fa-solid fa-xmark text-xl"></i>
        </button>
      </div>
    </div>
    
    <!-- Modal Body -->
    <div class="p-6">
      <form id="bookingForm" action="{{ route('opd.book-appointment') }}" method="POST" class="space-y-4">
        @csrf
        
        <!-- Customer Name -->
        <div>
          <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-2">
            Full Name <span class="text-red-500">*</span>
          </label>
          <input type="text" 
                 id="customer_name" 
                 name="customer_name" 
                 value="{{ Auth::user()->fname ?? '' }} {{ Auth::user()->lname ?? '' }}"
                 placeholder="Enter your full name" 
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                 required>
        </div>
        
        <!-- Customer Email -->
        <div>
          <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">
            Email Address <span class="text-red-500">*</span>
          </label>
          <input type="email" 
                 id="customer_email" 
                 name="customer_email" 
                 value="{{ Auth::user()->email ?? '' }}"
                 placeholder="Enter your email address" 
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                 required>
        </div>
        
        <!-- Appointment Date -->
        <div>
          <label for="appointment_date" class="block text-sm font-semibold text-gray-700 mb-2">
            Preferred Date <span class="text-red-500">*</span>
          </label>
          <input type="date" 
                 id="appointment_date" 
                 name="appointment_date" 
                 min="{{ date('Y-m-d') }}"
                 max="{{ date('Y-m-d', strtotime('+3 months')) }}"
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                 required>
        </div>
        
        <!-- Appointment Time -->
        <div>
          <label for="appointment_time" class="block text-sm font-semibold text-gray-700 mb-2">
            Preferred Time <span class="text-red-500">*</span>
          </label>
          <select id="appointment_time" 
                  name="appointment_time" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  required>
            <option value="">Select time slot</option>
            <option value="08:00">8:00 AM</option>
            <option value="08:30">8:30 AM</option>
            <option value="09:00">9:00 AM</option>
            <option value="09:30">9:30 AM</option>
            <option value="10:00">10:00 AM</option>
            <option value="10:30">10:30 AM</option>
            <option value="11:00">11:00 AM</option>
            <option value="11:30">11:30 AM</option>
            <option value="13:00">1:00 PM</option>
            <option value="13:30">1:30 PM</option>
            <option value="14:00">2:00 PM</option>
            <option value="14:30">2:30 PM</option>
            <option value="15:00">3:00 PM</option>
            <option value="15:30">3:30 PM</option>
            <option value="16:00">4:00 PM</option>
            <option value="16:30">4:30 PM</option>
          </select>
        </div>
        
        <!-- Selected Tests Summary -->
        <div class="bg-gray-50 rounded-lg p-4">
          <h4 class="font-semibold text-gray-900 mb-2">Selected Tests:</h4>
          <div id="modalCartItems" class="space-y-1 text-sm">
            <!-- Will be populated by JavaScript -->
          </div>
          <div class="border-t border-gray-200 mt-3 pt-3 flex justify-between font-semibold">
            <span>Total:</span>
            <span id="modalCartTotal" class="text-blue-600">₱0.00</span>
          </div>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
          <div class="flex items-center justify-center">
            <i class="fa-solid fa-calendar-check mr-2"></i>
            Book Appointment
          </div>
        </button>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    
    // Search functionality
    const searchInput = document.getElementById('searchTests');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceFilter = document.getElementById('priceFilter');
    
    function filterTests() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedPriceRange = priceFilter.value;
        
        document.querySelectorAll('.test-card').forEach(card => {
            const testName = card.dataset.testName;
            const categoryId = card.dataset.categoryId;
            const price = parseFloat(card.dataset.price);
            
            let showCard = true;
            
            // Search filter
            if (searchTerm && !testName.includes(searchTerm)) {
                showCard = false;
            }
            
            // Category filter
            if (selectedCategory && categoryId !== selectedCategory) {
                showCard = false;
            }
            
            // Price filter
            if (selectedPriceRange) {
                if (selectedPriceRange === '0-500' && price > 500) showCard = false;
                if (selectedPriceRange === '500-1000' && (price <= 500 || price > 1000)) showCard = false;
                if (selectedPriceRange === '1000-2000' && (price <= 1000 || price > 2000)) showCard = false;
                if (selectedPriceRange === '2000+' && price <= 2000) showCard = false;
            }
            
            card.style.display = showCard ? 'block' : 'none';
        });
        
        // Show/hide category sections
        document.querySelectorAll('.category-section').forEach(section => {
            const visibleCards = section.querySelectorAll('.test-card[style="display: block"], .test-card:not([style*="display: none"])');
            section.style.display = visibleCards.length > 0 ? 'block' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterTests);
    categoryFilter.addEventListener('change', filterTests);
    priceFilter.addEventListener('change', filterTests);
    
    // Category toggle
    document.querySelectorAll('.toggle-category').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.category;
            const content = document.getElementById(`category-${categoryId}`);
            const icon = this.querySelector('i');
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            } else {
                content.style.display = 'none';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            }
        });
    });
    
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const testId = this.dataset.testId;
            const testName = this.dataset.testName;
            const testPrice = parseFloat(this.dataset.testPrice);
            const categoryId = this.dataset.categoryId;
            const categoryName = this.dataset.categoryName;
            
            // Check if already in cart
            if (cart.find(item => item.id === testId)) {
                alert('Test already added to cart');
                return;
            }
            
            // Check if there's already a test from this category
            const existingCategoryItem = cart.find(item => item.categoryId === categoryId);
            if (existingCategoryItem) {
                const confirmReplace = confirm(`You already have "${existingCategoryItem.name}" from ${categoryName} category. Do you want to replace it with "${testName}"?`);
                if (!confirmReplace) {
                    return;
                }
                
                // Remove the existing item from the category
                cart = cart.filter(item => item.categoryId !== categoryId);
                
                // Reset the button state of the previously selected test
                const prevButton = document.querySelector(`[data-test-id="${existingCategoryItem.id}"]`);
                if (prevButton) {
                    prevButton.innerHTML = '<i class="fa-solid fa-plus mr-1"></i> Add';
                    prevButton.classList.remove('bg-green-600');
                    prevButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    prevButton.disabled = false;
                }
            }
            
            // Add the new test to cart
            cart.push({
                id: testId,
                name: testName,
                price: testPrice,
                categoryId: categoryId,
                categoryName: categoryName
            });
            
            updateCartDisplay();
            this.innerHTML = '<i class="fa-solid fa-check mr-1"></i> Added';
            this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            this.classList.add('bg-green-600');
            this.disabled = true;
        });
    });
    
    function updateCartDisplay() {
        const cartSummary = document.getElementById('cartSummary');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        
        if (cart.length === 0) {
            cartSummary.classList.add('hidden');
            return;
        }
        
        cartSummary.classList.remove('hidden');
        
        cartItems.innerHTML = cart.map(item => `
            <div class="flex items-center justify-between text-sm border-b border-gray-100 pb-2 mb-2 last:border-b-0 last:pb-0 last:mb-0">
                <div class="flex-1">
                    <div class="font-medium truncate">${item.name}</div>
                    <div class="text-xs text-gray-500">${item.categoryName}</div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-medium">₱${item.price.toFixed(2)}</span>
                    <button class="remove-item text-red-600 hover:text-red-700" data-test-id="${item.id}">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>
        `).join('');
        
        const total = cart.reduce((sum, item) => sum + item.price, 0);
        cartTotal.textContent = `₱${total.toFixed(2)}`;
        
        // Add remove functionality
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const testId = this.dataset.testId;
                cart = cart.filter(item => item.id !== testId);
                
                // Reset button state
                const addButton = document.querySelector(`[data-test-id="${testId}"]`);
                if (addButton) {
                    addButton.innerHTML = '<i class="fa-solid fa-plus mr-1"></i> Add';
                    addButton.classList.remove('bg-green-600');
                    addButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    addButton.disabled = false;
                }
                
                updateCartDisplay();
            });
        });
    }
    
    // Clear cart
    document.getElementById('clearCart').addEventListener('click', function() {
        cart = [];
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.innerHTML = '<i class="fa-solid fa-plus mr-1"></i> Add';
            button.classList.remove('bg-green-600');
            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            button.disabled = false;
        });
        updateCartDisplay();
    });
    
    // Proceed to booking
    document.getElementById('proceedToBooking').addEventListener('click', function() {
        if (cart.length === 0) return;
        openBookingModal();
    });
    
    // Booking Modal Functions
    function openBookingModal() {
        const modal = document.getElementById('bookingModal');
        const modalContent = document.getElementById('bookingModalContent');
        
        // Update modal cart items
        updateModalCartItems();
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Trigger animation
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    function closeBookingModal() {
        const modal = document.getElementById('bookingModal');
        const modalContent = document.getElementById('bookingModalContent');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
    
    function updateModalCartItems() {
        const modalCartItems = document.getElementById('modalCartItems');
        const modalCartTotal = document.getElementById('modalCartTotal');
        
        modalCartItems.innerHTML = cart.map(item => `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-medium">${item.name}</div>
                    <div class="text-xs text-gray-500">${item.categoryName}</div>
                </div>
                <span class="font-medium">₱${item.price.toFixed(2)}</span>
            </div>
        `).join('');
        
        const total = cart.reduce((sum, item) => sum + item.price, 0);
        modalCartTotal.textContent = `₱${total.toFixed(2)}`;
    }
    
    // Close modal events
    document.getElementById('closeBookingModal').addEventListener('click', closeBookingModal);
    
    // Close modal when clicking outside
    document.getElementById('bookingModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeBookingModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeBookingModal();
        }
    });
    
    // Handle form submission
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (cart.length === 0) {
            alert('Please select at least one test');
            return;
        }
        
        const formData = new FormData(this);
        const customerName = formData.get('customer_name');
        const customerEmail = formData.get('customer_email');
        const appointmentDate = formData.get('appointment_date');
        const appointmentTime = formData.get('appointment_time');
        
        // Prepare data for submission
        const bookingData = new FormData();
        bookingData.append('_token', document.querySelector('input[name="_token"]').value);
        bookingData.append('customer_name', customerName);
        bookingData.append('customer_email', customerEmail);
        bookingData.append('appointment_date', appointmentDate);
        bookingData.append('appointment_time', appointmentTime);
        
        // Add selected tests as array
        cart.forEach((test, index) => {
            bookingData.append(`selected_tests[${index}][id]`, test.id);
            bookingData.append(`selected_tests[${index}][name]`, test.name);
            bookingData.append(`selected_tests[${index}][price]`, test.price);
        });
        
        // Debug: Log what we're sending
        console.log('Sending booking data:');
        console.log('Customer Name:', customerName);
        console.log('Customer Email:', customerEmail);
        console.log('Appointment Date:', appointmentDate);
        console.log('Appointment Time:', appointmentTime);
        console.log('Cart contents:', cart);
        
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Booking...';
        submitButton.disabled = true;
        
        // Submit booking
        fetch('{{ route("opd.book-appointment") }}', {
            method: 'POST',
            body: bookingData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                alert(data.message);
                
                // Clear cart and close modal
                cart = [];
                updateCartDisplay();
                closeBookingModal();
                
                // Reset all add buttons
                document.querySelectorAll('.add-to-cart').forEach(button => {
                    button.innerHTML = '<i class="fa-solid fa-plus mr-1"></i> Add';
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    button.disabled = false;
                });
                
                // Optionally redirect to show page to see booked appointments
                if (confirm('Would you like to view your appointments?')) {
                    window.location.href = "{{ route('opd.show') }}";
                }
            } else {
                alert('Booking failed: ' + data.message);
                if (data.errors && data.errors.length > 0) {
                    console.error('Booking errors:', data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Booking error:', error);
            alert('An error occurred while booking. Please check the console for details.');
        })
        .finally(() => {
            // Reset button state
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });
});
</script>

@endsection
