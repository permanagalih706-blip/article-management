<?php

namespace App\Http\Controllers;

use App\Models\AllowedWord;
use Illuminate\Http\Request;

class AllowedWordController extends Controller
{
    /**
     * Display the list of blocked words.
     */
    public function index()
    {
        $words = AllowedWord::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.allowed-words', compact('words'));
    }

    /**
     * Store a new blocked word.
     */
    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:255|unique:allowed_words,word',
        ]);

        AllowedWord::create([
            'word'       => strtolower(trim($request->word)),
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Kata berhasil ditambahkan ke daftar blokir!');
    }

    /**
     * Remove a blocked word.
     */
    public function destroy(AllowedWord $allowedWord)
    {
        $allowedWord->delete();

        return redirect()->back()->with('success', 'Kata berhasil dihapus dari daftar blokir!');
    }
}
