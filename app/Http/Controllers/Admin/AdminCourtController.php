<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class AdminCourtController extends Controller
{
    public function index()
    {
        $courts = Court::withCount('bookings')->get();
        return view('admin.courts.index', compact('courts'));
    }

    public function create()
    {
        return view('admin.courts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'floor_type' => ['required', 'string', 'max:100'],
            'price_per_hour' => ['required', 'integer', 'min:1000'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:available,maintenance'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lapangan wajib diisi.',
            'floor_type.required' => 'Jenis lantai wajib dipilih atau diisi.',
            'price_per_hour.required' => 'Tarif sewa per jam wajib diisi.',
            'price_per_hour.min' => 'Tarif sewa minimal Rp 1.000.',
            'status.required' => 'Status lapangan wajib dipilih.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'court_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/courts');
            if (! file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $imagePath = 'uploads/courts/' . $filename;
        }

        Court::create([
            'name' => $validated['name'],
            'floor_type' => $validated['floor_type'],
            'price_per_hour' => $validated['price_per_hour'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.courts.index')->with('success', 'Data lapangan baru berhasil ditambahkan.');
    }

    public function edit(Court $court)
    {
        return view('admin.courts.edit', compact('court'));
    }

    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'floor_type' => ['required', 'string', 'max:100'],
            'price_per_hour' => ['required', 'integer', 'min:1000'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:available,maintenance'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lapangan wajib diisi.',
            'floor_type.required' => 'Jenis lantai wajib dipilih atau diisi.',
            'price_per_hour.required' => 'Tarif sewa per jam wajib diisi.',
            'price_per_hour.min' => 'Tarif sewa minimal Rp 1.000.',
            'status.required' => 'Status lapangan wajib dipilih.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $imagePath = $court->image;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'court_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/courts');
            if (! file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);

            // Delete old file if exists
            if ($court->image && file_exists(public_path($court->image))) {
                @unlink(public_path($court->image));
            }
            $imagePath = 'uploads/courts/' . $filename;
        }

        $court->update([
            'name' => $validated['name'],
            'floor_type' => $validated['floor_type'],
            'price_per_hour' => $validated['price_per_hour'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.courts.index')->with('success', 'Data lapangan berhasil diperbarui.');
    }

    public function destroy(Court $court)
    {
        if ($court->image && file_exists(public_path($court->image))) {
            @unlink(public_path($court->image));
        }

        $court->delete();

        return redirect()->route('admin.courts.index')->with('success', 'Data lapangan berhasil dihapus.');
    }
}
