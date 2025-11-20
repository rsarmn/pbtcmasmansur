<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarAdminController extends Controller
{
    // List semua kamar
    public function index()
    {
        $kamars = Kamar::orderBy('jenis_kamar')->get();

        return view('admin.kamar.index', compact('kamars'));
    }

    // Update harga & foto
    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $request->validate([
            'harga' => 'required|numeric|min:0',
            'foto'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $kamar->harga = $request->harga;

        if ($request->hasFile('foto')) {
            // simpan ke storage/app/public/kamar
            $path = $request->file('foto')->store('kamar', 'public');
            $kamar->foto = $path;
        }

        $kamar->save();

        return redirect()
            ->route('admin.kamar.index')
            ->with('success', 'Data kamar berhasil diperbarui.');
    }
}
