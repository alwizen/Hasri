<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function create()
    {
        $classRooms = ClassRoom::all();
        return view('students.create', compact('classRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|unique:students',
            'full_name' => 'required|string|max:255',
            'class_room_id' => 'required|exists:class_rooms,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            // 'status' => 'required|in:active,inactive',
        ], [
            'nis.required' => 'NIS wajib diisi',
            'nis.unique' => 'Data NIS sudah terdaftar',
            'full_name.required' => 'Nama lengkap wajib diisi',
            'class_room_id.required' => 'Kelas wajib dipilih',
            'class_room_id.exists' => 'Kelas tidak valid',
            // 'status.required' => 'Status wajib dipilih',
        ]);

        Student::create($validated);

        return redirect()->route('siswa.create')->with('success', 'Data siswa berhasil ditambahkan!');
    }
}
