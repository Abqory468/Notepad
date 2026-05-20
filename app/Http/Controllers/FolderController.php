<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function index()
    {
        $folders = Auth::user()->folders()->orderBy('name')->get();
        return response()->json($folders);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder = Auth::user()->folders()->create([
            'name' => $request->name,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'folder' => $folder
            ]);
        }

        return back()->with('status', 'Folder created successfully!');
    }


    public function update(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder->update([
            'name' => $request->name,
        ]);

        return back()->with('status', 'Folder updated successfully!');
    }

    public function destroy(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        // Note: With cascading deletes on the DB or model, this might delete notes or just nullify folder_id
        // In our migration, we used `nullOnDelete()`, so the notes will just lose their folder.
        $folder->delete();

        return back()->with('status', 'Folder deleted successfully!');
    }
}
