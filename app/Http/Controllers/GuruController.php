<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::latest()->paginate(10);
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip'       => 'required|unique:gurus,nip',
        ]);

        Guru::create($validated);
        return redirect()->route('guru.index')->with('success', 'Data Guru berhasil ditambahkan.');
    }

    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip'       => 'required|unique:gurus,nip,' . $guru->id,
        ]);

        $guru->update($validated);
        return redirect()->route('guru.index')->with('success', 'Data Guru diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return back()->with('success', 'Data Guru berhasil dihapus.');
    }
}