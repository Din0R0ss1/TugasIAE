# Perpustakaan Microservices

Aplikasi manajemen perpustakaan berbasis microservices dengan arsitektur API Aggregator pattern.

## Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                      Client                                  │
└──────────────────────────┬──────────────────────────────────┘
                           │ REST API
                    ┌──────▼──────┐
                    │   Gateway   │  Port 8000
                    │  (Laravel)  │  JWT Auth + API Aggregator
                    └──┬───┬───┬──┘
                       │   │   │
          GraphQL      │   │   │  REST API
        ┌──────────────┘   │   └──────────────┐
        │                  │                   │
 ┌──────▼──────┐   ┌──────▼──────┐    ┌──────▼──────┐
 │User Service  │   │   Hasura    │    │Loan Service  │
 │ (Lighthouse) │   │  GraphQL    │    │  (REST API)  │
 │  Port 8001   │   │  Port 8080  │    │  Port 8003   │
 └──────┬──────┘   └──────┬──────┘    └──────┬──────┘
        │                  │                   │
    ┌───▼───┐         ┌───▼───┐          ┌───▼───┐
    │ MySQL │         │ PgSQL │          │ MySQL │
    │user_db│         │book_db│          │loan_db│
    └───────┘         └───┬───┘          └───────┘
                          │
                    ┌─────▼─────┐
                    │ Book      │
                    │ Worker    │ (RabbitMQ consumer)
                    └─────┬─────┘
                          │
                    ┌─────▼─────┐
                    │  RabbitMQ │  Port 5672 / 15672
                    └───────────┘
```

## Struktur Folder

```
TugasIAE/
├── gateway-perpus/     # API Gateway (Laravel, port 8000)
├── user-service/       # User Service (Laravel + Lighthouse GraphQL, MySQL, port 8001)
├── book-service/       # Book Service (Laravel queue worker + PostgreSQL)
├── loan-service/       # Loan Service (Laravel REST API, MySQL, port 8003)
├── hasura/             # Hasura GraphQL Engine (port 8080)
├── rabbitmq/           # RabbitMQ message broker (port 5672, 15672)
├── start-all.sh        # Script untuk start semua services (bash)
├── start-all.ps1       # Script untuk start semua services (PowerShell)
├── stop-all.sh         # Script untuk stop semua services (bash)
├── stop-all.ps1        # Script untuk stop semua services (PowerShell)
└── README.md
```

## Service Endpoints

| Service | Port | Protocol | Keterangan |
|---------|------|----------|------------|
| Gateway | 8000 | REST API | API Aggregator + JWT Auth |
| User Service | 8001 | GraphQL (`/graphql`) + REST | Manajemen user |
| Hasura | 8080 | GraphQL | Book data (via PostgreSQL) |
| Loan Service | 8003 | REST API | Manajemen peminjaman |
| RabbitMQ | 5672 | AMQP | Message broker |
| RabbitMQ UI | 15672 | HTTP | Management dashboard |

## Cara Menjalankan

### Start semua services

**Windows (PowerShell):**
```powershell
.\start-all.ps1
```

**Linux/Mac (Bash):**
```bash
./start-all.sh
```

### Stop semua services

**Windows (PowerShell):**
```powershell
.\stop-all.ps1
```

**Linux/Mac (Bash):**
```bash
./stop-all.sh
```

### Urutan Startup

1. RabbitMQ (message broker)
2. Book Service (PostgreSQL + queue worker)
3. Hasura (GraphQL Engine, connected to PostgreSQL)
4. User Service (Lighthouse GraphQL + MySQL)
5. Loan Service (REST API + MySQL)
6. Gateway (API Aggregator)

## Gateway API Endpoints

Semua endpoint di bawah memerlukan JWT token (kecuali auth).

### Auth
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| POST | `/api/auth/login` | Login |
| POST | `/api/auth/register` | Register |
| POST | `/api/auth/logout` | Logout |
| GET | `/api/auth/me` | Get current user |

### Books (via Hasura GraphQL)
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/books` | List semua buku |
| POST | `/api/books` | Tambah buku baru |
| PUT | `/api/books/{id}` | Update buku |

### Users (via User Service GraphQL)
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/users` | List semua user |
| POST | `/api/users` | Tambah user baru |
| PUT | `/api/users/{id}` | Update user |
| GET | `/api/users/{id}/history` | Riwayat peminjaman user |

### Loans (via REST API)
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/loans` | List semua peminjaman |
| POST | `/api/loans` | Buat peminjaman baru |
| PUT | `/api/loans/{id}/return` | Kembalikan buku |

## Komunikasi Antar Service

### Synchronous
- **Gateway → Hasura**: GraphQL queries/mutations untuk data buku
- **Gateway → User Service**: GraphQL queries/mutations untuk data user
- **Gateway → Loan Service**: REST API untuk peminjaman
- **Loan Service → User Service**: REST API untuk validasi user
- **Loan Service → Hasura**: GraphQL untuk validasi dan update stok buku

### Asynchronous (RabbitMQ)
- **Loan Service → `book-loan` queue**: Event peminjaman (kurangi stok)
- **Book Worker**: Memproses `book-loan` queue (kurangi stok buku)
- **Loan Service → `loan-history` queue**: Event riwayat peminjaman
- **Loan History Worker**: Memproses `loan-history` queue (simpan riwayat di user-service)

## Database

| Service | Database | Engine |
|---------|----------|--------|
| User Service | `user_db` | MySQL 8.0 |
| Book Service | `book_db` | PostgreSQL 15 |
| Loan Service | `loan_db` | MySQL 8.0 |

## Development (per-service)

Untuk menjalankan service secara individual:

```bash
cd <service-folder>
docker compose up -d --build
```

Pastikan network `perpus-network` sudah dibuat:
```bash
docker network create perpus-network --driver bridge
```
