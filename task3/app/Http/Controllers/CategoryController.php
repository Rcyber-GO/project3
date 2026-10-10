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
     *
     * Setelah Activity memakai soft delete, kegiatan di Trash MASIH merujuk kategorinya
     * (barisnya ada, hanya deleted_at terisi), sehingga restore tetap aman. Karena itu
     * pengecekan memakai withTrashed(); tanpanya, aplikasi akan lolos mengecek tetapi
     * ditolak foreign key di database.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->activities()->withTrashed()->exists()) {
            return back()->with(
                'error',
                "Kategori \"{$category->name}\" tidak dapat dihapus karena masih dipakai oleh kegiatan (termasuk yang ada di Trash)."
            );
        }

        try {
            $category->delete();
        } catch (QueryException) {
            return back()->with(
                'error',
                "Kategori \"{$category->name}\" tidak dapat dihapus karena masih terhubung dengan data lain."
            );
        }

        return back()->with('success', "Kategori \"{$category->name}\" berhasil dihapus.");
    }
}
