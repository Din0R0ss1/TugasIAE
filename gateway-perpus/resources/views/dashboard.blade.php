<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SiPerpus Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(160deg, #0f172a 0%, #1e3a8a 50%, #0f172a 100%);
    min-height: 100vh;
}
.login-box {
    background: rgba(255,255,255,0.07);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 16px;
    padding: 40px 36px;
    width: 100%;
    max-width: 380px;
}
.login-title {
    color: white;
    font-size: 26px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 28px;
    letter-spacing: 0.5px;
}
.field-wrap {
    border-bottom: 1.5px solid rgba(255,255,255,0.3);
    display: flex;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 8px;
}
.field-wrap input {
    background: transparent;
    border: none;
    outline: none;
    color: white;
    font-size: 14px;
    width: 100%;
    padding: 4px 0;
}
.field-wrap input::placeholder { color: rgba(255,255,255,0.5); }
.field-wrap input:-webkit-autofill,
.field-wrap input:-webkit-autofill:hover,
.field-wrap input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0px 1000px transparent inset !important;
    -webkit-text-fill-color: white !important;
    transition: background-color 5000s ease-in-out 0s;
    background-color: transparent !important;
    caret-color: white;
}
.field-wrap .eye-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: rgba(255,255,255,0.5);
    font-size: 16px;
    padding: 0 4px;
    transition: color 0.2s;
}
.field-wrap .eye-btn:hover { color: white; }
.row-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}
.row-options label {
    color: rgba(255,255,255,0.6);
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
}
.row-options input[type=checkbox] { accent-color: #3b82f6; }
.btn-login {
    width: 100%;
    padding: 13px;
    background: white;
    color: #1e3a8a;
    border: none;
    border-radius: 50px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    letter-spacing: 0.3px;
}
.btn-login:hover {
    background: #e0e7ff;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
.error-box {
    background: rgba(239,68,68,0.2);
    border: 1px solid rgba(239,68,68,0.4);
    color: #fca5a5;
    font-size: 13px;
    padding: 10px 14px;
    border-radius: 8px;
    margin-bottom: 16px;
    display: none;
}
</style>
</head>
<body>

<!-- ===================== LOGIN PAGE ===================== -->
<div id="loginPage" style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:16px;">
    <div class="login-box">
        <h1 class="login-title">Login</h1>

        <!-- Error -->
        <div class="error-box" id="loginError">
            <span id="loginErrorMsg"></span>
        </div>

        <!-- Email / Username -->
        <div class="field-wrap">
            <input id="loginEmail" type="email" placeholder="Email"
                onkeydown="if(event.key==='Enter') document.getElementById('loginPassword').focus()">
            <svg width="18" height="18" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
        </div>

        <!-- Password -->
        <div class="field-wrap">
            <input id="loginPassword" type="password" placeholder="Password"
                onkeydown="if(event.key==='Enter') doLogin()">
            <button class="eye-btn" onclick="togglePassword()" id="toggleBtn" title="Tampilkan password">
                <svg id="eyeIcon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
            </button>
        </div>

        <!-- Tombol Login -->
        <button onclick="doLogin()" class="btn-login">Login</button>
    </div>
</div>

<!-- ===================== DASHBOARD ===================== -->
<div id="dashboardPage" style="display:none; height:100vh; flex-direction:row;">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-logo">
                <span>SiPerpus</span>
            </div>
            <p id="adminName" class="admin-name"></p>
            <nav style="margin-top:8px;">
                <button onclick="showTab('books', this)" class="menu active">
                    <span class="menu-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span> Buku
                </button>
                <button onclick="showTab('users', this)" class="menu">
                    <span class="menu-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span> User
                </button>
                <button onclick="showTab('loans', this)" class="menu">
                    <span class="menu-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg></span> Peminjaman
                </button>
            </nav>
        </div>
        <button onclick="doLogout()" class="logout-btn">
            Logout
        </button>
    </aside>

    <!-- MAIN -->
    <main class="dash-main">
        <!-- TOPBAR -->
        <div class="topbar">
            <div>
                <h2 class="page-title" id="pageTitle">Kelola Buku</h2>
                <p class="page-sub">Selamat datang di panel admin SiPerpus</p>
            </div>
            <div class="quote-text">"Sebuah buku dapat merubah dari yang tidak tahu menjadi berilmu"</div>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
                <div>
                    <p class="stat-label">Total Buku</p>
                    <h2 id="totalBooks" class="stat-num">0</h2>
                </div>
            </div>
            <div class="stat-card stat-purple">
                <div class="stat-icon"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                <div>
                    <p class="stat-label">Total User</p>
                    <h2 id="totalUsers" class="stat-num">0</h2>
                </div>
            </div>
            <div class="stat-card stat-teal">
                <div class="stat-icon"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg></div>
                <div>
                    <p class="stat-label">Total Peminjaman</p>
                    <h2 id="totalLoans" class="stat-num">0</h2>
                </div>
            </div>
        </div>

        <!-- BOOKS -->
        <section id="booksTab">
            <div class="form-card">
                <div class="form-row">
                    <input id="judul" placeholder="Judul Buku" class="dash-input">
                    <input id="penulis" placeholder="Penulis" class="dash-input">
                    <input id="stok" type="number" placeholder="Stok" class="dash-input" style="max-width:100px;">
                    <button onclick="tambahBuku()" class="btn-add">+ Tambah Buku</button>
                </div>
            </div>
            <div class="table-card">
                <table class="dash-table">
                    <thead><tr><th>ID</th><th>Judul</th><th>Penulis</th><th>Stok</th><th>Aksi</th></tr></thead>
                    <tbody id="booksTable"></tbody>
                </table>
            </div>
        </section>

        <!-- USERS -->
        <section id="usersTab" style="display:none;">
            <div class="form-card">
                <div class="form-row">
                    <input id="namaUser" placeholder="Nama User" class="dash-input">
                    <input id="emailUser" placeholder="Email" class="dash-input">
                    <button onclick="tambahUser()" class="btn-add">+ Tambah User</button>
                </div>
            </div>
            <div class="table-card">
                <table class="dash-table">
                    <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Aksi</th></tr></thead>
                    <tbody id="usersTable"></tbody>
                </table>
            </div>
        </section>

        <!-- LOANS -->
        <section id="loansTab" style="display:none;">
            <div class="form-card">
                <div class="form-row">
                    <select id="userId" class="dash-select"></select>
                    <select id="bookId" class="dash-select"></select>
                    <button onclick="tambahPeminjaman()" class="btn-add">+ Pinjam</button>
                </div>
            </div>
            <div class="table-card">
                <table class="dash-table">
                    <thead><tr><th>User</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody id="loansTable"></tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- STYLE DASHBOARD -->
<style>
/* ===== SIDEBAR ===== */
.sidebar {
    width: 240px; min-height: 100vh;
    background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);
    display: flex; flex-direction: column;
    justify-content: space-between;
    padding: 24px 16px;
    flex-shrink: 0;
}
.sidebar-logo {
    display: flex; align-items: center; gap: 10px;
    color: white; font-size: 20px; font-weight: 700;
    margin-bottom: 4px; padding: 0 8px;
}
.admin-name {
    color: rgba(255,255,255,0.45);
    font-size: 12px; padding: 0 8px;
    margin-bottom: 24px;
}
.menu {
    display: flex; align-items: center; gap: 10px;
    width: 100%; text-align: left;
    padding: 11px 14px; border-radius: 10px;
    border: none; background: transparent;
    color: rgba(255,255,255,0.65);
    font-size: 14px; font-weight: 500;
    cursor: pointer; transition: all 0.2s;
    margin-bottom: 4px;
}
.menu:hover { background: rgba(255,255,255,0.1); color: white; }
.menu.active { background: #3b82f6; color: white; box-shadow: 0 4px 12px rgba(59,130,246,0.4); }
.menu-icon { font-size: 16px; display: inline-flex; align-items: center; }
.logout-btn {
    width: 100%; padding: 11px;
    background: rgba(239,68,68,0.15);
    border: 1px solid rgba(239,68,68,0.3);
    color: #fca5a5; border-radius: 10px;
    font-size: 13px; font-weight: 500;
    cursor: pointer; transition: all 0.2s;
}
.logout-btn:hover { background: rgba(239,68,68,0.3); color: white; }

/* ===== MAIN ===== */
.dash-main {
    flex: 1; overflow-y: auto;
    background: #f0f4ff;
    padding: 28px 32px;
}
.topbar {
    display: flex; justify-content: space-between;
    align-items: flex-start; margin-bottom: 24px;
}
.page-title { font-size: 26px; font-weight: 700; color: #1e293b; }
.page-sub { font-size: 13px; color: #94a3b8; margin-top: 2px; }
.quote-text { font-size: 12px; color: #94a3b8; font-style: italic; max-width: 320px; text-align: right; }

/* ===== STAT CARDS ===== */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
.stat-card {
    border-radius: 16px; padding: 20px 24px;
    display: flex; align-items: center; gap: 16px;
    color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.stat-blue    { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-purple  { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
.stat-teal    { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.stat-icon { font-size: 28px; display: flex; align-items: center; opacity: 0.9; }
.stat-label { font-size: 12px; opacity: 0.85; }
.stat-num { font-size: 28px; font-weight: 700; }

/* ===== FORM CARD ===== */
.form-card {
    background: white; border-radius: 14px;
    padding: 16px 20px; margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.form-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.dash-input {
    padding: 10px 14px; border: 1.5px solid #e2e8f0;
    border-radius: 8px; font-size: 14px; outline: none;
    transition: all 0.2s; background: #f8fafc; flex: 1; min-width: 120px;
}
.dash-input:focus { border-color: #3b82f6; background: white; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.dash-select {
    padding: 10px 14px; border: 1.5px solid #e2e8f0;
    border-radius: 8px; font-size: 14px; outline: none;
    background: #f8fafc; flex: 1; cursor: pointer;
}
.dash-select:focus { border-color: #3b82f6; }
.btn-add {
    padding: 10px 20px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white; border: none; border-radius: 8px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    transition: all 0.2s; white-space: nowrap;
    box-shadow: 0 4px 10px rgba(59,130,246,0.3);
}
.btn-add:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(59,130,246,0.4); }

/* ===== TABLE ===== */
.table-card {
    background: white; border-radius: 14px;
    overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.dash-table { width: 100%; border-collapse: collapse; }
.dash-table thead tr { background: #f8fafc; }
.dash-table th {
    padding: 13px 16px; text-align: left;
    font-size: 12px; font-weight: 600;
    color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;
}
.dash-table td {
    padding: 13px 16px; font-size: 14px;
    color: #334155; border-bottom: 1px solid #f1f5f9;
}
.dash-table tbody tr:hover { background: #f8faff; }
.dash-table tbody tr:last-child td { border-bottom: none; }
.badge-dipinjam {
    background: #fef3c7; color: #d97706;
    padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
}
.badge-selesai {
    background: #d1fae5; color: #059669;
    padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
}
</style>

<!-- SCRIPT -->
<script>
const BASE = '/api';

// ============================================================
// JWT HELPER
// ============================================================
function getToken() {
    return localStorage.getItem('jwt_token');
}

function authHeaders() {
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${getToken()}`
    };
}

// ============================================================
// AUTH — CEK LOGIN SAAT PAGE LOAD
// ============================================================
window.addEventListener('DOMContentLoaded', () => {
    const token = getToken();
    if (token) {
        showDashboard();
    } else {
        showLoginPage();
    }
});

function showLoginPage() {
    document.getElementById('loginPage').style.display  = 'flex';
    document.getElementById('dashboardPage').style.display = 'none';
}

function showDashboard() {
    document.getElementById('loginPage').style.display     = 'none';
    document.getElementById('dashboardPage').style.display = 'flex';
    document.getElementById('dashboardPage').style.flexDirection = 'row';

    // tampilkan nama admin
    const name = localStorage.getItem('admin_name');
    document.getElementById('adminName').innerText = `Halo, ${name}`;

    loadBooks();
    loadUsers();
    loadLoans();
    document.querySelector('.menu').classList.add('active');
}

// ============================================================
// LOGIN
// ============================================================
// HELPER LOGIN
// ============================================================
function showLoginError(msg) {
    const errBox = document.getElementById('loginError');
    document.getElementById('loginErrorMsg').innerText = msg;
    errBox.style.display = 'block';
}

function togglePassword() {
    const input = document.getElementById('loginPassword');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        // tampilkan password → ganti ke mata normal
        input.type = 'text';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    } else {
        // sembunyikan password → ganti ke mata dicoret
        input.type = 'password';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
    }
}

// ============================================================
async function doLogin() {
    const email    = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const errBox   = document.getElementById('loginError');
    errBox.style.display = 'none';
    document.getElementById('loginErrorMsg').innerText = '';

    if (!email || !password) {
        showLoginError('Email dan password wajib diisi');
        return;
    }

    try {
        const res  = await fetch(`${BASE}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await res.json();

        if (!res.ok) {
            showLoginError(data.message ?? 'Login gagal');
            return;
        }

        // simpan token & info user
        localStorage.setItem('jwt_token',  data.token);
        localStorage.setItem('admin_name', data.user.name);
        showDashboard();

    } catch (err) {
        showLoginError('Server error, coba lagi');
    }
}

// ============================================================
// LOGOUT
// ============================================================
async function doLogout() {
    if (!confirm("Yakin mau logout?")) return;
    try {
        await fetch(`${BASE}/auth/logout`, {
            method: 'POST',
            headers: authHeaders()
        });
    } catch (_) {}
    localStorage.removeItem('jwt_token');
    localStorage.removeItem('admin_name');
    showLoginPage();
}

// ============================================================
// HANDLE 401 — token expired/invalid
// ============================================================
async function authFetch(url, options = {}) {
    if (!options.headers) options.headers = authHeaders();
    try {
        const res = await fetch(url, options);
        if (res.status === 401) {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('admin_name');
            alert("Sesi habis, silakan login ulang");
            showLoginPage();
            return null;
        }
        return res;
    } catch (err) {
        console.error('Request gagal:', url, err);
        return null;
    }
}

// ============================================================
// TAB SWITCH
// ============================================================
function showTab(tab, el) {
    document.getElementById('booksTab').style.display  = 'none';
    document.getElementById('usersTab').style.display  = 'none';
    document.getElementById('loansTab').style.display  = 'none';
    document.getElementById(tab + 'Tab').style.display = 'block';
    document.querySelectorAll('.menu').forEach(btn => btn.classList.remove('active'));
    el.classList.add('active');
    // update page title
    const titles = { books: 'Kelola Buku', users: 'Kelola User', loans: 'Peminjaman' };
    document.getElementById('pageTitle').innerText = titles[tab];
}

// ============================================================
// BOOKS
// ============================================================
async function loadBooks() {
    const res = await authFetch(`${BASE}/books`);
    if (!res) return;
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
                    style="background:#fef9c3;color:#ca8a04;border:1px solid #fde68a;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</button>
            </td>
        </tr>`;
    });
}

async function tambahBuku() {
    const judul   = document.getElementById('judul').value;
    const penulis = document.getElementById('penulis').value;
    const stok    = document.getElementById('stok').value;
    if (!judul || !penulis || !stok) { alert("Isi semua field"); return; }

    const res  = await authFetch(`${BASE}/books`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ judul, penulis, stok })
    });
    if (!res) return;
    const data = await res.json();
    if (!res.ok) { alert(data.message); return; }
    alert("Buku berhasil ditambahkan");
    loadBooks();
    document.getElementById('judul').value = '';
    document.getElementById('penulis').value = '';
    document.getElementById('stok').value = '';
}

function editBuku(id, judul, penulis, stok) {
    const newJudul   = prompt("Judul:", judul);
    const newPenulis = prompt("Penulis:", penulis);
    const newStok    = prompt("Stok:", stok);
    if (!newJudul || !newPenulis || !newStok) return;
    updateBuku(id, newJudul, newPenulis, newStok);
}

async function updateBuku(id, judul, penulis, stok) {
    const res  = await authFetch(`${BASE}/books/${id}`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify({ judul, penulis, stok })
    });
    if (!res) return;
    const data = await res.json();
    if (!res.ok) { alert(data.message); return; }
    alert("Buku berhasil diupdate");
    loadBooks();
}

// ============================================================
// USERS
// ============================================================
async function loadUsers() {
    const res = await authFetch(`${BASE}/users`);
    if (!res) return;
    const data = await res.json();
    document.getElementById('totalUsers').innerText = data.length;
    const table = document.getElementById('usersTable');
    table.innerHTML = '';
    data.forEach(u => {
        table.innerHTML += `
        <tr>
            <td>${u.id}</td>
            <td>${u.name}</td>
            <td>${u.email}</td>
            <td>
                <button onclick="editUser(${u.id}, '${u.name}', '${u.email}')"
                    style="background:#fef9c3;color:#ca8a04;border:1px solid #fde68a;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</button>
            </td>
        </tr>`;
    });
}

async function tambahUser() {
    const name  = document.getElementById('namaUser').value.trim();
    const email = document.getElementById('emailUser').value.trim();

    if (!name || !email) { alert("Isi semua field"); return; }

    // Validasi harus @gmail.com
    if (!email.endsWith('@gmail.com')) {
        alert("Email harus menggunakan @gmail.com");
        document.getElementById('emailUser').focus();
        return;
    }

    const res  = await authFetch(`${BASE}/users`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ name, email })
    });
    if (!res) return;
    const data = await res.json();
    if (!res.ok) { alert(data.message); return; }
    alert("User berhasil ditambahkan");
    loadUsers();
    document.getElementById('namaUser').value  = '';
    document.getElementById('emailUser').value = '';
}

function editUser(id, name, email) {
    const newName  = prompt("Nama:", name);
    if (newName === null) return;
    const newEmail = prompt("Email (harus @gmail.com):", email);
    if (newEmail === null) return;

    if (!newName.trim() || !newEmail.trim()) {
        alert("Nama dan email tidak boleh kosong");
        return;
    }
    if (!newEmail.endsWith('@gmail.com')) {
        alert("Email harus menggunakan @gmail.com");
        return;
    }
    updateUser(id, newName.trim(), newEmail.trim());
}

async function updateUser(id, name, email) {
    const res  = await authFetch(`${BASE}/users/${id}`, {
        method: 'PUT',
        headers: authHeaders(),
        body: JSON.stringify({ name, email })
    });
    if (!res) return;
    const data = await res.json();
    if (!res.ok) { alert(data.message); return; }
    alert("User berhasil diupdate");
    loadUsers();
}

// ============================================================
// LOANS
// ============================================================
async function loadLoans() {
    const [loanRes, userRes, bookRes] = await Promise.all([
        authFetch(`${BASE}/loans`),
        authFetch(`${BASE}/users`),
        authFetch(`${BASE}/books`)
    ]);
    if (!loanRes || !userRes || !bookRes) return;

    const loans = await loanRes.json();
    const users = await userRes.json();
    const books = await bookRes.json();

    const userSelect = document.getElementById('userId');
    userSelect.innerHTML = '';
    users.forEach(u => {
        userSelect.innerHTML += `<option value="${u.id}">${u.name}</option>`;
    });

    const bookSelect = document.getElementById('bookId');
    bookSelect.innerHTML = '';
    books.forEach(b => {
        bookSelect.innerHTML += `
            <option value="${b.id}" ${b.stok == 0 ? 'disabled' : ''}>
                ${b.judul} (stok: ${b.stok})
            </option>`;
    });

    const userMap = {};
    users.forEach(u => { userMap[u.id] = u.name; });
    const bookMap = {};
    books.forEach(b => { bookMap[b.id] = b.judul; });

    document.getElementById('totalLoans').innerText = loans.length;
    const table = document.getElementById('loansTable');
    table.innerHTML = '';
    loans.forEach(l => {
        table.innerHTML += `
        <tr>
            <td>${userMap[l.user_id] ?? 'Unknown'}</td>
            <td>${bookMap[l.book_id] ?? 'Unknown'}</td>
            <td>${l.loan_date}</td>
            <td>${l.return_date ?? '-'}</td>
            <td><span class="${l.status === 'dipinjam' ? 'badge-dipinjam' : 'badge-selesai'}">${l.status}</span></td>
            <td>
                ${l.status === 'dipinjam'
                    ? `<button onclick="kembalikan(${l.id})"
                            style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 14l-4-4 4-4"/><path d="M5 10h11a4 4 0 1 1 0 8h-1"/></svg> Kembalikan</button>`
                    : `<span style="color:#059669;font-size:13px;display:inline-flex;align-items:center;gap:4px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Selesai</span>`
                }
            </td>
        </tr>`;
    });
}

async function tambahPeminjaman() {
    const user_id = document.getElementById('userId').value;
    const book_id = document.getElementById('bookId').value;
    if (!user_id || !book_id) { alert("Pilih User dan Buku dulu"); return; }

    const res  = await authFetch(`${BASE}/loans`, {
        method: 'POST',
        headers: authHeaders(),
        body: JSON.stringify({ user_id, book_id })
    });
    if (!res) return;
    const data = await res.json();
    if (!res.ok) { alert(data.message); return; }
    alert("Peminjaman berhasil!");
    loadLoans();
}

async function kembalikan(id) {
    if (!confirm("Yakin buku sudah dikembalikan?")) return;
    const res  = await authFetch(`${BASE}/loans/${id}/return`, {
        method: 'PUT',
        headers: authHeaders()
    });
    if (!res) return;
    const data = await res.json();
    if (!res.ok) { alert(data.message); return; }
    alert(data.message);
    loadLoans();
}
</script>
</body>
</html>
