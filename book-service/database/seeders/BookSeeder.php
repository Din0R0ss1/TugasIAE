<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['judul' => 'Laravel untuk Pemula', 'penulis' => 'Budi Santoso', 'stok' => 10],
            ['judul' => 'Belajar Microservices', 'penulis' => 'Andi Wijaya', 'stok' => 5],
            ['judul' => 'Mastering PHP', 'penulis' => 'Siti Rahayu', 'stok' => 8],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(
                ['judul' => $book['judul']],
                $book
            );
        }
    }
}
