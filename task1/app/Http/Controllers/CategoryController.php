<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::ordered()->withCount('activities')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * BR-08: kategori yang masih dipakai kegiatan tidak boleh dihapus.
     * Lapisan 1: pengecekan aplikasi -> pesan yang jelas untuk pengguna.
     * Lapisan 2: foreign key restrictOnDelete di database -> jaring pengaman terakhir.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->activities()->exists()) {
            return back()->with(
                'error',
                "Kategori \"{$category->name}\" tidak dapat dihapus karena masih dipakai oleh kegiatan."
            );
        }

        try {
            $category->delete();
        } catch (QueryException $e) {
            // Misalnya ada kegiatan yang baru dibuat di antara pengecekan dan penghapusan.
            return back()->with(
                'error',
                "Kategori \"{$category->name}\" tidak dapat dihapus karena masih terhubung dengan data lain."
            );
        }

        return back()->with('success', "Kategori \"{$category->name}\" berhasil dihapus.");
    }
}
