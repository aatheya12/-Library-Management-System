<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index()
    {
        $books = Book::latest()->get();

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255|unique:books,isbn',
            'quantity' => 'required|integer|min:1',
        ]);

        $validated['available_quantity'] = $validated['quantity'];

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book added successfully!');
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255|unique:books,isbn,' . $book->id,
            'quantity' => 'required|integer|min:1',
        ]);

        $borrowed = $book->quantity - $book->available_quantity;

        $validated['available_quantity'] = max(
            0,
            $validated['quantity'] - $borrowed
        );

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Book deleted successfully!');
    }
}
