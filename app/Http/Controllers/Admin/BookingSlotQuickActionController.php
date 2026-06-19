<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSlot;
use Illuminate\Http\Request;

class BookingSlotQuickActionController extends Controller
{
    public function toggleStatus(BookingSlot $slot)
    {
        $newStatus = $slot->status === 'tersedia' ? 'nonaktif' : 'tersedia';
        $slot->update(['status' => $newStatus]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status slot berhasil diubah',
            'new_status' => $newStatus
        ]);
    }

    public function toggleAll(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|in:tersedia,nonaktif'
        ]);

        BookingSlot::where('tanggal', $request->tanggal)
            ->where('status', '!=', 'penuh')
            ->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status semua slot berhasil diubah'
        ]);
    }
}