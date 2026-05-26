<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NoteController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $notes = Auth::user()->notes()
            ->when($request->search, function($query, $search) {
                return $query->search($search);
            })
            ->when($request->folder_id, function($query, $folder_id) {
                return $query->where('folder_id', $folder_id);
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        $folders = Auth::user()->folders()->orderBy('name')->get();

        return view('notes.index', compact('notes', 'folders'));
    }

    public function create()
    {
        $folders = Auth::user()->folders()->orderBy('name')->get();
        return view('notes.create', compact('folders'));
    }

    public function store(Request $request)
    {
            $validated = $request->validate([
                'title' => 'required|max:255',
                'content' => 'required',
                'color' => 'nullable|string',
                'folder_id' => 'nullable|exists:folders,id',
            ]);

        $validated['user_id'] = Auth::id();

        Note::create($validated);

        return redirect()->route('notes.index')
            ->with('success', 'Note created successfully!');
    }

    public function show(Note $note)
    {
        return redirect()->route('notes.edit', $note);
    }

    public function edit(Note $note)
    {
        $this->authorize('update', $note);
        $folders = Auth::user()->folders()->orderBy('name')->get();
        return view('notes.edit', compact('note', 'folders'));
    }

    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'color' => 'nullable|string',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $note->update($validated);

        return redirect()->route('notes.index')
            ->with('success', 'Note updated successfully!');
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Note deleted successfully!');
    }

    public function togglePin(Note $note)
    {
        $this->authorize('update', $note);
        $note->update(['is_pinned' => !$note->is_pinned]);

        return back()->with('success', 
            $note->is_pinned ? 'Note pinned!' : 'Note unpinned!');
    }
}