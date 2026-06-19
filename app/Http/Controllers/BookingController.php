<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\BookingSlot;
use App\Models\OperationalDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    // Slot configuration: 30-minute slots, 4 cars per slot by default
    private int $slotMinutes = 30;
    private int $slotCapacity = 4;
    private int $openHour = 8;   // 08:00
    private int $closeHour = 18; // 18:00

    public function create()
    {
        $serviceId = request()->query('service_id');
        $discountId = request()->query('discount_id');
        
        $selectedService = null;
        $selectedDiscount = null;
        
        if ($serviceId) {
            $selectedService = Service::find($serviceId);
        }
        
        if ($discountId) {
            $selectedDiscount = Discount::where('active', true)
                ->whereDate('expires_at', '>=', now())
                ->find($discountId);
        }

        $services = Service::orderBy('name')->get();
        $activeDiscounts = Discount::with('service')
            ->where('active', true)
            ->whereDate('expires_at', '>=', now())
            ->orderBy('expires_at', 'asc')
            ->get();

        // Load active payment methods for the payment step in the booking form
        $paymentMethods = PaymentMethod::active()->ordered()->get();

        return view('booking_form_simple', [
            'services' => $services,
            'selectedService' => $selectedService,
            'selectedDiscount' => $selectedDiscount,
            'activeDiscounts' => $activeDiscounts,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Return list of available dates (within range) that have at least one available BookingSlot.
     * GET /api/slot-availability?range_days=7
     */
    public function availableDates(Request $request)
    {
        $rangeDays = (int) $request->input('range_days', 7);
        $rangeDays = max(1, min($rangeDays, 14));

        $start = now()->startOfDay();
        $end = now()->addDays($rangeDays)->endOfDay();

        Log::debug('Checking available dates:', [
            'start' => $start->toDateString(),
            'end' => $end->toDateString()
        ]);

        $dates = BookingSlot::select('tanggal')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'tersedia')
            ->where(function($query) {
                $query->whereNull('terisi')
                    ->orWhereRaw('kapasitas > terisi');
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->pluck('tanggal')
            ->map(function ($d) {
                return Carbon::parse($d)->toDateString();
            });

        return response()->json([
            'status' => 'success',
            'data' => $dates,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'selected_slot_id' => 'required|integer|exists:booking_slots,id',
            'license_plate' => 'required|string|max:20',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'discount_id' => 'nullable|integer|exists:discounts,id',
        ]);

        // Ensure selected discount (if any) belongs to the chosen service and is active/not expired
        if (!empty($validatedData['discount_id'])) {
            $isValidDiscount = Discount::where('id', $validatedData['discount_id'])
                ->where('service_id', $validatedData['service_id'])
                ->where('active', true)
                ->whereDate('expires_at', '>=', now())
                ->exists();

            if (!$isValidDiscount) {
                return back()->withErrors(['discount_id' => 'Diskon tidak valid untuk layanan yang dipilih.'])->withInput();
            }
        }

        $service = Service::findOrFail($validatedData['service_id']);
        
        // Find the booking slot
        $bookingSlot = BookingSlot::where('id', $validatedData['selected_slot_id'])
            ->where('status', 'tersedia')
            ->first();
            
        if (!$bookingSlot) {
            return back()->withErrors(['selected_slot_id' => 'Slot yang dipilih tidak tersedia.'])->withInput();
        }
        
        // Check if slot is still available
        if ($bookingSlot->terisi >= $bookingSlot->kapasitas) {
            return back()->withErrors(['selected_slot_id' => 'Slot yang dipilih sudah penuh.'])->withInput();
        }
        
        // Create booking datetime - gunakan tanggal dari slot dan jam mulai
        $bookingDateTime = \Carbon\Carbon::parse($bookingSlot->tanggal)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($bookingSlot->jam_mulai)->format('H:i:s');
        
        // Generate queue number within the slot
        $queueNumber = $bookingSlot->terisi + 1;

        // Calculate pricing
        $originalPrice = $service->price;
        $discountPercent = 0;
        $finalPrice = $originalPrice;
        $discountName = null;

        $appliedDiscount = null;
        if (!empty($validatedData['discount_id'])) {
            $appliedDiscount = Discount::where('id', $validatedData['discount_id'])
                ->where('service_id', $validatedData['service_id'])
                ->where('active', true)
                ->whereDate('expires_at', '>=', now())
                ->first();
            
            if ($appliedDiscount) {
                $discountPercent = $appliedDiscount->percent;
                $discountAmount = floor($originalPrice * ($discountPercent / 100));
                $finalPrice = $originalPrice - $discountAmount;
                $discountName = $appliedDiscount->name;
            }
        }
        
        // Ensure final price is not negative
        $finalPrice = max(0, $finalPrice);

        // Debug log
        Log::info('Creating order with pricing:', [
            'original_price' => $originalPrice,
            'discount_percent' => $discountPercent,
            'final_price' => $finalPrice,
            'discount_name' => $discountName,
            'discount_id' => $validatedData['discount_id'] ?? null
        ]);
        
        $order = Order::create([
            'user_id' => auth()->id(),
            'service_type' => $service->name,
            'booking_date' => $bookingDateTime,
            'license_plate' => $validatedData['license_plate'],
            'queue_number' => $queueNumber,
            'status' => 'Pending Pembayaran',
            'original_price' => $originalPrice,
            'discount_percent' => $discountPercent,
            'final_price' => $finalPrice,
            'discount_name' => $discountName,
            'payment_status' => 'pending',
        ]);
        
        // Update booking slot
        $bookingSlot->increment('terisi');
        if ($bookingSlot->terisi >= $bookingSlot->kapasitas) {
            $bookingSlot->update(['status' => 'penuh']);
        }

        return redirect()->route('payment.confirmation', $order->id)
            ->with('success', 'Pesanan berhasil dibuat! Silakan pilih metode pembayaran.');
    }

    private function normalizeToSlotStart(string $dateTime): string
    {
        $dt = now()->parse($dateTime);
        $minute = (int) floor($dt->minute / $this->slotMinutes) * $this->slotMinutes;
        $normalized = $dt->copy()->setTime($dt->hour, $minute, 0, 0);
        return $normalized->toDateTimeString();
    }

    private function slotEndFromStart(string $slotStart): string
    {
        return now()->parse($slotStart)->addMinutes($this->slotMinutes)->toDateTimeString();
    }

    public function status()
    {
        $activeOrders = Order::whereIn('status', ['Menunggu', 'Proses'])
            ->orderBy('queue_number', 'asc')
            ->get();
        
        return view('status', compact('activeOrders'));
    }

    public function history()
    {
        $orders = Order::where('hidden_from_user', false)
            ->where(function($query) {
                $query->where('payment_status', 'paid')
                      ->orWhere('payment_status', 'verified')
                      ->orWhereNotNull('payment_proof');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('history', compact('orders'));
    }

    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        
        // Only allow hiding from user view for specific statuses
        if ($order->canDeleteFromHistory()) {
            $order->update(['hidden_from_user' => true]);
            return redirect()->route('booking.history')->with('success', 'Pesanan berhasil dihapus dari riwayat!');
        }
        
        return redirect()->route('booking.history')->with('error', 'Pesanan ini tidak dapat dihapus dari riwayat!');
    }

    public function requestRefund(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        if (!$order->canRequestRefund()) {
            return redirect()->route('booking.history')->with('error', 'Refund tidak dapat diajukan untuk pesanan ini!');
        }
        
        $validated = $request->validate([
            'refund_reason' => 'required|string|max:500'
        ]);
        
        $order->update([
            'refund_reason' => $validated['refund_reason'],
            'refund_requested_at' => now()
        ]);
        
        return redirect()->route('booking.history')->with('success', 'Pengajuan refund berhasil dikirim!');
    }

    public function priceQuote(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'discount_id' => 'nullable|integer|exists:discounts,id',
        ]);

        $service = Service::findOrFail($request->input('service_id'));
        $basePrice = (int) $service->price;

        $percent = 0;
        if ($request->filled('discount_id')) {
            $discount = Discount::where('id', $request->input('discount_id'))
                ->where('service_id', $service->id)
                ->where('active', true)
                ->whereDate('expires_at', '>=', now())
                ->first();
            if ($discount) {
                $percent = (int) $discount->percent;
            }
        }

        $discountAmount = (int) floor($basePrice * ($percent / 100));
        $finalPrice = max(0, $basePrice - $discountAmount);

        return response()->json([
            'basePrice' => $basePrice,
            'discountPercent' => $percent,
            'discountAmount' => $discountAmount,
            'finalPrice' => $finalPrice,
        ]);
    }

    public function slotAvailability(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'booking_date' => 'required|date',
        ]);

        $slotStart = $this->normalizeToSlotStart($request->input('booking_date'));
        $count = Order::whereBetween('booking_date', [
            $slotStart,
            $this->slotEndFromStart($slotStart)
        ])->count();

        $remaining = max(0, $this->slotCapacity - $count);

        // Estimate wait time based on queue position and service duration
        $service = Service::findOrFail($request->input('service_id'));
        $durationMinutes = (int) ($service->duration ?? 30);
        $estimatedWaitMinutes = max(0, ($count) * $durationMinutes);

        return response()->json([
            'slotStart' => $slotStart,
            'capacity' => $this->slotCapacity,
            'booked' => $count,
            'remaining' => $remaining,
            'available' => $remaining > 0,
            'estimatedWaitMinutes' => $estimatedWaitMinutes,
        ]);
    }

    public function slotsForDate(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'date' => 'required|date',
        ]);

        try {
            $date = Carbon::parse($request->input('date'))->startOfDay()->toDateString();
            $service = Service::findOrFail($request->input('service_id'));
            
            Log::debug('Fetching slots for date:', [
                'date' => $date,
                'service_id' => $service->id
            ]);
            
            // First try to use admin-defined booking_slots table for the date
            $bookingSlots = BookingSlot::where('tanggal', $date)
                ->where('status', '!=', 'nonaktif')
                ->orderBy('jam_mulai')
                ->get();

            $slots = [];
            foreach ($bookingSlots as $bs) {
                // Convert time strings to Carbon instances in app timezone
                $jamMulai = Carbon::parse($bs->jam_mulai)->format('H:i');
                $jamSelesai = Carbon::parse($bs->jam_selesai)->format('H:i');
                $tersedia = $bs->kapasitas - ($bs->terisi ?? 0);
                
                $slots[] = [
                    'id' => $bs->id,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'kapasitas' => $bs->kapasitas,
                    'terisi' => $bs->terisi ?? 0,
                    'tersedia' => $tersedia,
                    'status' => $tersedia > 0 ? 'tersedia' : 'penuh',
                    'label' => $jamMulai . ' - ' . $jamSelesai
                ];
            }
            
            Log::debug('Total slots found:', ['count' => count($slots)]);

            return response()->json([
                'status' => 'success',
                'data' => $slots,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in slotsForDate:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch slots: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getDiscounts(Request $request)
    {
        $serviceId = $request->input('service_id');
        
        $discounts = Discount::where('service_id', $serviceId)
            ->where('active', true)
            ->whereDate('expires_at', '>=', now())
            ->select('id', 'name', 'percent', 'service_id')
            ->get();
            
        return response()->json($discounts);
    }
    
    public function getAvailableSchedules(Request $request)
    {
        try {
            $slots = \DB::table('booking_slots')
                ->where('status', 'tersedia')
                ->where('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal')
                ->orderBy('jam_mulai')
                ->get();
                
            if ($slots->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => []
                ]);
            }
            
            $schedules = [];
            $groupedSlots = [];
            
            // Group by date
            foreach ($slots as $slot) {
                $dateKey = $slot->tanggal;
                if (!isset($groupedSlots[$dateKey])) {
                    $groupedSlots[$dateKey] = [];
                }
                $groupedSlots[$dateKey][] = $slot;
            }
            
            // Format output
            foreach ($groupedSlots as $date => $daySlots) {
                $carbonDate = \Carbon\Carbon::parse($date);
                
                $schedules[] = [
                    'tanggal' => $date,
                    'tanggal_formatted' => $carbonDate->format('d M Y'),
                    'hari' => $carbonDate->locale('id')->isoFormat('dddd'),
                    'slots' => array_map(function ($slot) {
                        $jamMulai = \Carbon\Carbon::parse($slot->jam_mulai);
                        $jamSelesai = \Carbon\Carbon::parse($slot->jam_selesai);
                        
                        return [
                            'id' => $slot->id,
                            'jam_mulai' => $jamMulai->format('H:i'),
                            'jam_selesai' => $jamSelesai->format('H:i'),
                            'kapasitas' => $slot->kapasitas,
                            'terisi' => $slot->terisi ?? 0,
                            'tersedia' => $slot->kapasitas - ($slot->terisi ?? 0)
                        ];
                    }, $daySlots)
                ];
            }
                
            return response()->json([
                'status' => 'success',
                'data' => $schedules
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function showPaymentProof($filename)
    {
        $paths = [
            storage_path('app/public/payment-proofs/' . $filename),
            storage_path('app/public/payment_proofs/' . $filename)
        ];
        
        $path = null;
        foreach ($paths as $p) {
            if (\Illuminate\Support\Facades\File::exists($p)) {
                $path = $p;
                break;
            }
        }
        
        if (!$path) {
            abort(404);
        }

        $file = \Illuminate\Support\Facades\File::get($path);
        $type = \Illuminate\Support\Facades\File::mimeType($path);

        return \Illuminate\Support\Facades\Response::make($file, 200)->header('Content-Type', $type);
    }
}
