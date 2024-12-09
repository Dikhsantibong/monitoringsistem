<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index()
    {
        $issues = Issue::all(); // Ambil semua laporan masalah
        return view('admin.issues.index', compact('issues'));
    }

    public function edit($id)
    {
        $issue = Issue::findOrFail($id);
        return view('admin.issues.edit', compact('issue'));
    }

    public function update(Request $request, $id)
    {
        // Update laporan masalah
    }

    public function close($id)
    {
        // Menutup laporan masalah
    }
} 