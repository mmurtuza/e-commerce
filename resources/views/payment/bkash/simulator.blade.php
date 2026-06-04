<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bKash Tokenized Checkout Simulator</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f1f1f1;
            font-family: ui-sans-serif, system-ui, sans-serif;
        }
        .bkash-pink {
            background-color: #e2136e;
        }
        .bkash-text-pink {
            color: #e2136e;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between items-center py-4 bg-gray-100">
    
    <!-- Top Spacer/Branding -->
    <div class="w-full max-w-md flex justify-center mb-6">
        <div class="h-12 w-auto flex items-center justify-center font-black text-2xl tracking-wider text-gray-800">
            <span class="bkash-text-pink">bKash</span> Checkout
        </div>
    </div>

    <!-- Main Simulator Box -->
    <div class="w-full max-w-md bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-200" 
         x-data="{ 
            step: 1, 
            phone: '01712345678', 
            otp: '123456', 
            pin: '12345',
            errorMsg: '',
            validatePhone() {
                if (this.phone.length < 11) {
                    this.errorMsg = 'Please enter a valid bKash account number (11 digits)';
                } else {
                    this.errorMsg = '';
                    this.step = 2;
                }
            },
            validateOtp() {
                if (this.otp.length < 6) {
                    this.errorMsg = 'Please enter the 6-digit OTP code';
                } else {
                    this.errorMsg = '';
                    this.step = 3;
                }
            }
         }">
         
        <!-- Header Strip -->
        <div class="bkash-pink text-white px-6 py-5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <!-- bKash Logo Drawing (Pink Circle on White) -->
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md">
                    <span class="bkash-text-pink font-extrabold text-xs">bKash</span>
                </div>
                <div>
                    <h2 class="text-sm font-light text-pink-100">Merchant Payment</h2>
                    <p class="text-md font-bold">Dinajpur IT Park</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs text-pink-100 block">Amount</span>
                <span class="text-lg font-bold">৳ {{ number_format((float) $order->total, 2) }}</span>
            </div>
        </div>

        <!-- Order Summary Strip -->
        <div class="bg-pink-50 border-b border-pink-100 px-6 py-3 flex justify-between text-xs text-gray-600">
            <span>Invoice: <strong class="text-gray-800">{{ $order->order_number }}</strong></span>
            <span>Transaction ID: <strong class="text-gray-800">{{ substr($payment->transaction_id, 0, 15) }}...</strong></span>
        </div>

        <!-- Error Alert -->
        <div x-show="errorMsg" x-text="errorMsg" class="bg-red-50 text-red-600 text-xs px-6 py-2.5 border-b border-red-100 transition"></div>

        <!-- Form Wizards -->
        <div class="p-6 min-h-[250px] flex flex-col justify-between">
            
            <!-- STEP 1: Phone Number -->
            <div x-show="step === 1" x-transition class="space-y-4">
                <div class="text-center py-2">
                    <p class="text-sm text-gray-600">Enter your bKash account number</p>
                </div>
                <div class="relative">
                    <input type="text" x-model="phone" placeholder="e.g. 01XXXXXXXXX" maxlength="11"
                           class="w-full border-2 border-gray-200 focus:border-[#e2136e] rounded-lg px-4 py-3 outline-none text-center font-mono text-lg tracking-widest text-gray-800">
                </div>
                <p class="text-2xs text-gray-400 text-center">Your account number is usually your 11-digit mobile number</p>
            </div>

            <!-- STEP 2: OTP Verification -->
            <div x-show="step === 2" x-transition class="space-y-4">
                <div class="text-center py-2">
                    <p class="text-sm text-gray-600">Enter verification code sent to <span class="font-bold text-gray-800" x-text="phone"></span></p>
                    <p class="text-xs text-gray-400 mt-1">(Simulator: enter any 6 digits e.g. 123456)</p>
                </div>
                <div>
                    <input type="text" x-model="otp" placeholder="Enter 6-digit OTP" maxlength="6"
                           class="w-full border-2 border-gray-200 focus:border-[#e2136e] rounded-lg px-4 py-3 outline-none text-center font-mono text-lg tracking-widest text-gray-800">
                </div>
                <div class="text-center">
                    <button @click="step = 1" class="text-xs text-gray-500 hover:text-gray-700 underline">Change Number</button>
                </div>
            </div>

            <!-- STEP 3: PIN Verification -->
            <div x-show="step === 3" x-transition class="space-y-4">
                <div class="text-center py-2">
                    <p class="text-sm text-gray-600">Enter PIN of your bKash account</p>
                    <p class="text-xs text-gray-400 mt-1">(Simulator: enter any 5-digit PIN e.g. 12345)</p>
                </div>
                <div>
                    <input type="password" x-model="pin" placeholder="Enter 5-digit PIN" maxlength="5"
                           class="w-full border-2 border-gray-200 focus:border-[#e2136e] rounded-lg px-4 py-3 outline-none text-center font-mono text-lg tracking-widest text-gray-800">
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-6 flex gap-4">
                <!-- Close Button -->
                <a href="{{ route('payment.bkash.callback', ['status' => 'cancel', 'paymentID' => $payment->transaction_id]) }}" 
                   class="flex-1 py-3 text-center border-2 border-gray-200 text-gray-600 font-semibold rounded-lg hover:bg-gray-50 hover:text-gray-800 transition">
                    Cancel
                </a>

                <!-- Next / Submit Button -->
                <button x-show="step === 1" @click="validatePhone()" 
                        class="flex-1 py-3 bkash-pink text-white font-semibold rounded-lg hover:opacity-90 transition">
                    Proceed
                </button>
                <button x-show="step === 2" @click="validateOtp()" 
                        class="flex-1 py-3 bkash-pink text-white font-semibold rounded-lg hover:opacity-90 transition">
                    Proceed
                </button>
                <a x-show="step === 3" 
                   href="{{ route('payment.bkash.callback', ['status' => 'success', 'paymentID' => $payment->transaction_id]) }}"
                   class="flex-1 py-3 text-center bkash-pink text-white font-semibold rounded-lg hover:opacity-90 transition">
                    Confirm
                </a>
            </div>

        </div>

        <!-- Footer terms -->
        <div class="bg-gray-50 border-t border-gray-100 px-6 py-4 text-center text-3xs text-gray-400 leading-relaxed">
            By clicking confirm, you agree to bKash's <span class="underline cursor-pointer">Terms & Conditions</span>.
            <br>
            For support call 16247 or visit bkash.com
        </div>
    </div>

    <!-- Quick Developer Helper -->
    <div class="w-full max-w-md mt-6 bg-yellow-50 border-2 border-dashed border-yellow-200 rounded-xl p-4 text-xs text-yellow-800">
        <h4 class="font-bold mb-2">🛠️ Developer Utilities (Testing options):</h4>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('payment.bkash.callback', ['status' => 'success', 'paymentID' => $payment->transaction_id]) }}" 
               class="bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700 transition">
                ⚡ Force Success
            </a>
            <a href="{{ route('payment.bkash.callback', ['status' => 'cancel', 'paymentID' => $payment->transaction_id]) }}" 
               class="bg-gray-600 text-white px-3 py-1.5 rounded hover:bg-gray-700 transition">
                ⚡ Force Cancel
            </a>
            <a href="{{ route('payment.bkash.callback', ['status' => 'failed', 'paymentID' => $payment->transaction_id]) }}" 
               class="bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700 transition">
                ⚡ Force Failure
            </a>
        </div>
    </div>

</body>
</html>
