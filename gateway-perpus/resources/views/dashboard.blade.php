<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard Perpustakaan</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body { font-family: 'Inter', sans-serif; }
</style>
</head>

<body class="bg-gray-100">

<div class="flex h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white shadow-xl p-6">
        <h1 class="text-2xl font-bold mb-8">SiPerpus</h1>

        <nav class="space-y-3">
            <button onclick="showTab('books', this)" class="menu">📖 Buku</button>
            <button onclick="showTab('users', this)" class="menu">👤 User</button>
            <button onclick="showTab('loans', this)" class="menu">🔄 Peminjaman</button>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 p-8 overflow-y-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-semibold">Dashboard</h2>
            <span class="text-gray-500">"Sebuah buku dapat merubah dari yang tidak tahu menjadi berilmu"</span>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="card">
                <p class="text-gray-500">Total Buku</p>
                <h2 id="totalBooks" class="text-2xl font-bold">0</h2>
            </div>
            <div class="card">
                <p class="text-gray-500">Total User</p>
                <h2 id="totalUsers" class="text-2xl font-bold">0</h2>
            </div>
            <div class="card">
                <p class="text-gray-500">Total Peminjaman</p>
                <h2 id="totalLoans" class="text-2xl font-bold">0</h2>
            </div>
        </div>

        <!-- BOOKS -->
        <section id="booksTab">
            <h3 class="title">Kelola Buku</h3>
            <div class="mb-4 flex gap-2">
    <input id="judul" placeholder="Judul" class="border p-2 rounded">
    <input id="penulis" placeholder="Penulis" class="border p-2 rounded">
    <input id="stok" type="number" placeholder="Stok" class="border p-2 rounded w-24">

    <button onclick="tambahBuku()" 
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        + Tambah
    </button>
</div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="booksTable"></tbody>
                </table>
            </div>
        </section>

        <!-- USERS -->
        <section id="usersTab" class="hidden">
            <h3 class="title">Kelola User</h3>
            <div class="mb-4 flex gap-2">
    <input id="namaUser" placeholder="Nama" class="border p-2 rounded">
    <input id="emailUser" placeholder="Email" class="border p-2 rounded">

    <button onclick="tambahUser()" 
        class="bg-blue-500 text-white px-4 py-2 rounded">
        + Tambah User
    </button>
</div>
            
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable"></tbody>
                </table>
            </div>
        </section>

        <!-- LOANS -->
        <section id="loansTab" class="hidden">
            <h3 class="title">Peminjaman</h3>
              <div class="mb-4 flex gap-2">
              <select id="userId" class="border p-2 rounded"></select>
              <select id="bookId" class="border p-2 rounded"></select>

         <button onclick="tambahPeminjaman()" 
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            + Pinjam
        </button>
    </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="loansTable"></tbody>
                </table>
            </div>
        </section>

    </main>
</div>

<!-- STYLE -->
<style>
.menu {
    display: block;
    width: 100%;
    text-align: left;
    padding: 10px;
    border-radius: 8px;
    transition: 0.2s;
}
.menu:hover {
    background: #f3f4f6;
}

.menu.active {
    background: #3b82f6; /* biru */
    color: white;
}
.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 15px;
}

.table-wrap {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #f9fafb;
    text-align: left;
    padding: 12px;
    font-weight: 600;
}

.table td {
    padding: 12px;
    border-top: 1px solid #eee;
}
</style>

<!-- SCRIPT -->
<script>

// TAB SWITCH
function showTab(tab, el) {
    // sembunyikan semua tab
    document.getElementById('booksTab').classList.add('hidden');
    document.getElementById('usersTab').classList.add('hidden');
    document.getElementById('loansTab').classList.add('hidden');

    // tampilkan tab aktif
    document.getElementById(tab + 'Tab').classList.remove('hidden');

    // 🔥 hapus semua active
    document.querySelectorAll('.menu').forEach(btn => {
        btn.classList.remove('active');
    });

    // 🔥 tambahkan active ke yang diklik
    el.classList.add('active');
}

// FETCH BOOKS
async function loadBooks() {
    const res = await fetch('/api/books');
    const data = await res.json();

    document.getElementById('totalBooks').innerText = data.length;

    const table = document.getElementById('booksTable');
    table.innerHTML = '';

    data.forEach(b => {
        table.innerHTML += `
        <tr>
            <td>${b.id}</td>
            <td>${b.judul ?? '-'}</td>
            <td>${b.penulis ?? '-'}</td>
            <td>${b.stok ?? '-'}</td>
             <td>
        <button onclick="editBuku(${b.id}, '${b.judul}', '${b.penulis}', ${b.stok})"
            class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">
            Edit
        </button>
    </td>
</tr>
        </tr>`;
    });
}

// FETCH USERS
async function loadUsers() {
    const res = await fetch('/api/users');
    const data = await res.json();

    if (!res.ok) {
    alert(data.message); // 🔥 tampilkan error asli
    return;
}

    document.getElementById('totalUsers').innerText = data.length;

    const table = document.getElementById('usersTable');
    table.innerHTML = '';

    data.forEach(u => {
        table.innerHTML += `
        <tr>
            <td>${u.id}</td>
            <td>${u.name}</td>
            <td>${u.email}</td>
        </tr>`;
    });
}

// FETCH LOANS
async function loadLoans() {

    const [loanRes, userRes, bookRes] = await Promise.all([
        fetch('/api/loans'),
        fetch('/api/users'),
        fetch('/api/books')
    ]);

    const loans = await loanRes.json();
    const users = await userRes.json();
    const books = await bookRes.json();

    // isi dropdown user
const userSelect = document.getElementById('userId');
userSelect.innerHTML = '';

users.forEach(u => {
    userSelect.innerHTML += `<option value="${u.id}">${u.name}</option>`;
});

/// isi dropdown buku
const bookSelect = document.getElementById('bookId');
bookSelect.innerHTML = '';

books.forEach(b => {
    bookSelect.innerHTML += `
        <option value="${b.id}" ${b.stok == 0 ? 'disabled' : ''}>
            ${b.judul} (stok: ${b.stok})
        </option>
    `;
});

    // mapping user
    const userMap = {};
    users.forEach(u => {
        userMap[u.id] = u.name;
    });

    // mapping buku
    const bookMap = {};
    books.forEach(b => {
        bookMap[b.id] = b.judul;
    });

    const table = document.getElementById('loansTable');
    table.innerHTML = '';

    loans.forEach(l => {
        table.innerHTML += `
        <tr>
            <td>${userMap[l.user_id] ?? 'Unknown User'}</td>
            <td>${bookMap[l.book_id] ?? 'Unknown Book'}</td>
            <td>${l.loan_date}</td>
            <td>${l.return_date ?? '-'}</td>
            <td>${l.status}</td>
            <td>
    ${
        l.status === 'dipinjam'
        ? `<button onclick="kembalikan(${l.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Belum Kembali</button>`
        : `<span class="bg-green-500 px-3 py-1 text-white rounded text-sm">Selesai</span>`
    }
</td>
        </tr>`;
    });

    document.getElementById('totalLoans').innerText = loans.length;
}

async function kembalikan(id) {
    if (!confirm("Yakin buku sudah dikembalikan?")) return;

    try {
     const res = await fetch(`http://localhost:8003/api/loans/${id}/return`, {
     method: 'PUT'
     });

        const data = await res.json(); // 🔥 ambil response asli

        if (!res.ok) {
            alert(data.message); // 🔥 tampilkan pesan backend
            return;
        }

        await loadLoans();
        alert(data.message); // 🔥 tampilkan sukses dari backend

    } catch (err) {
        console.error(err);
        alert("Server tidak merespon / bukan JSON");
    }
}

async function tambahPeminjaman() {
    const user_id = document.getElementById('userId').value;
    const book_id = document.getElementById('bookId').value;

    if (!user_id || !book_id) {
        alert("Isi User ID dan Book ID dulu");
        return;
    }

    try {
        const res = await fetch('http://localhost:8003/api/loans', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: user_id,
                book_id: book_id
            })
        });

        const data = await res.json();

        if (!res.ok) {
            alert(data.message);
            return;
        }

        alert("Peminjaman berhasil!");
        loadLoans();

        // reset input
        document.getElementById('userId').value = '';
        document.getElementById('bookId').value = '';

    } catch (err) {
        console.error(err);
        alert("Server error");
    }
}

async function tambahBuku() {
    const judul = document.getElementById('judul').value;
    const penulis = document.getElementById('penulis').value;
    const stok = document.getElementById('stok').value;

    if (!judul || !penulis || !stok) {
        alert("Isi semua field");
        return;
    }

    const res = await fetch('http://localhost:8002/api/books', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ judul, penulis, stok })
    });

    const data = await res.json();

    if (!res.ok) {
        alert(data.message);
        return;
    }

    alert("Buku berhasil ditambahkan");
    loadBooks();

    // reset input
    document.getElementById('judul').value = '';
    document.getElementById('penulis').value = '';
    document.getElementById('stok').value = '';
}

function editBuku(id, judul, penulis, stok) {
    const newJudul = prompt("Judul:", judul);
    const newPenulis = prompt("Penulis:", penulis);
    const newStok = prompt("Stok:", stok);

    if (!newJudul || !newPenulis || !newStok) return;

    updateBuku(id, newJudul, newPenulis, newStok);  
}

async function updateBuku(id, judul, penulis, stok) {
    const res = await fetch(`http://localhost:8002/api/books/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ judul, penulis, stok })
    });

    const data = await res.json();

    if (!res.ok) {
        alert(data.message);
        return;
    }

    alert("Buku berhasil diupdate");
    loadBooks();
}

async function tambahUser() {
    const name = document.getElementById('namaUser').value;
    const email = document.getElementById('emailUser').value;

    if (!name || !email) {
        alert("Isi semua field");
        return;
    }

    try {
        const res = await fetch('http://localhost:8001/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, email })
        });

        const data = await res.json();

        if (!res.ok) {
            alert(data.message);
            return;
        }

        alert("User berhasil ditambahkan");
        loadUsers();

        document.getElementById('namaUser').value = '';
        document.getElementById('emailUser').value = '';

    } catch (err) {
        console.error(err);
        alert("Server error");
    }
}

// INIT
loadBooks();
loadUsers();
loadLoans();

document.querySelector('.menu').classList.add('active');

</script>

</body>
</html>