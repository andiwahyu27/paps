# Panduan GitHub Private untuk Custom GPT Auditor PAPS

## 1. Arsitektur yang Disarankan

Gunakan pola berikut:

```text
Custom GPT
    |
    | GPT Action, read-only
    v
API Proxy / GitHub App
    |
    | GitHub API
    v
Private Repository PAPS
```

Custom GPT tidak sebaiknya menyimpan atau menerima Personal Access Token langsung di prompt atau knowledge file.

## 2. Opsi Autentikasi

### Opsi A — GitHub App Read-only, direkomendasikan

Gunakan ini untuk penggunaan tim atau production.

Keuntungan:

- Akses dapat dibatasi hanya ke repository PAPS.
- Permission dapat dibuat read-only.
- Token dapat dirotasi tanpa mengubah akun personal.
- Aktivitas lebih mudah diaudit.

### Opsi B — Fine-grained Personal Access Token

Gunakan hanya untuk setup awal atau penggunaan pribadi.

Permission minimum:

- Repository access: pilih hanya repository PAPS.
- `Contents: Read-only`.
- `Pull requests: Read-only`.
- `Checks: Read-only`.
- `Metadata: Read-only`.

Jangan aktifkan permission write.

## 3. Membuat GitHub App

1. Buka GitHub Organization atau akun pemilik repository.
2. Masuk ke **Settings > Developer settings > GitHub Apps**.
3. Pilih **New GitHub App**.
4. Isi:

```text
GitHub App name: paps-audit-readonly
Homepage URL: URL service proxy Action
Webhook: Disabled
```

5. Pada **Repository permissions**, isi:

```text
Contents: Read-only
Pull requests: Read-only
Checks: Read-only
Metadata: Read-only
```

6. Pilih **Only select repositories**.
7. Pilih repository PAPS.
8. Buat App.
9. Generate **Private key** dan simpan di server proxy sebagai secret.
10. Install App ke repository PAPS.
11. Catat:

- App ID
- Installation ID
- Private key
- Organization/repository name

Private key jangan dimasukkan ke Custom GPT, knowledge file, repository, atau prompt.

## 4. Membuat API Proxy

GPT Action sebaiknya memanggil API proxy milik sendiri. Proxy bertanggung jawab untuk:

- membuat GitHub App installation token;
- memanggil GitHub API;
- membatasi repository ke PAPS;
- menolak path sensitif seperti `.env` dan private key;
- menghapus secret dari response;
- membatasi ukuran response;
- menerapkan rate limit;
- hanya menyediakan operasi GET/read-only.

Endpoint minimal:

```text
GET /github/repository
GET /github/tree
GET /github/file
GET /github/pull-request
GET /github/pull-request-files
GET /github/pull-request-diff
GET /github/checks
```

Contoh parameter:

```text
GET /github/file?path=app/Http/Controllers/HomeController.php&ref=main
GET /github/pull-request?number=123
GET /github/pull-request-diff?number=123
```

Proxy harus menolak:

```text
.env
.env.example jika berisi credential
*.pem
*.key
id_rsa
credentials.json
storage/logs/*
```

## 5. Konfigurasi Custom GPT Action

Di Custom GPT:

1. Buka **Configure**.
2. Masuk ke bagian **Actions**.
3. Pilih **Create new action**.
4. Masukkan URL OpenAPI schema dari proxy.
5. Atur authentication sesuai proxy.
6. Gunakan API key atau OAuth pada proxy, bukan token GitHub langsung.
7. Test setiap operasi GET.

Authentication yang disarankan:

```text
Type: API Key
Location: Header
Header name: X-GPT-Audit-Key
```

API key tersebut hanya untuk mengakses proxy dan harus berbeda dari GitHub token.

## 6. Contoh OpenAPI Schema

Schema berikut adalah contoh konseptual untuk proxy. Ganti `https://audit-api.example.com` dengan URL proxy sebenarnya.

```yaml
openapi: 3.1.0
info:
  title: PAPS GitHub Audit Read-only API
  version: 1.0.0
servers:
  - url: https://audit-api.example.com
security:
  - AuditApiKey: []
components:
  securitySchemes:
    AuditApiKey:
      type: apiKey
      in: header
      name: X-GPT-Audit-Key
  schemas:
    FileResponse:
      type: object
      properties:
        path:
          type: string
        ref:
          type: string
        content:
          type: string
        truncated:
          type: boolean
paths:
  /github/repository:
    get:
      operationId: getRepository
      summary: Get PAPS repository metadata
      responses:
        '200':
          description: Repository metadata
          content:
            application/json:
              schema:
                type: object
  /github/tree:
    get:
      operationId: getRepositoryTree
      summary: Get repository file tree
      parameters:
        - name: ref
          in: query
          required: false
          schema:
            type: string
            default: main
      responses:
        '200':
          description: File tree
          content:
            application/json:
              schema:
                type: object
  /github/file:
    get:
      operationId: getRepositoryFile
      summary: Get a non-sensitive repository file
      parameters:
        - name: path
          in: query
          required: true
          schema:
            type: string
        - name: ref
          in: query
          required: false
          schema:
            type: string
            default: main
      responses:
        '200':
          description: File content
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/FileResponse'
  /github/pull-request:
    get:
      operationId: getPullRequest
      summary: Get pull request metadata
      parameters:
        - name: number
          in: query
          required: true
          schema:
            type: integer
      responses:
        '200':
          description: Pull request metadata
          content:
            application/json:
              schema:
                type: object
  /github/pull-request-files:
    get:
      operationId: getPullRequestFiles
      summary: Get files changed by a pull request
      parameters:
        - name: number
          in: query
          required: true
          schema:
            type: integer
      responses:
        '200':
          description: Changed files
          content:
            application/json:
              schema:
                type: array
                items:
                  type: object
  /github/pull-request-diff:
    get:
      operationId: getPullRequestDiff
      summary: Get pull request diff
      parameters:
        - name: number
          in: query
          required: true
          schema:
            type: integer
      responses:
        '200':
          description: Pull request diff
          content:
            text/plain:
              schema:
                type: string
  /github/checks:
    get:
      operationId: getCommitChecks
      summary: Get CI checks for a commit or ref
      parameters:
        - name: ref
          in: query
          required: true
          schema:
            type: string
      responses:
        '200':
          description: CI checks
          content:
            application/json:
              schema:
                type: object
```

## 7. Pengaturan Prompt Tambahan

Tambahkan aturan berikut ke **Instructions** Custom GPT:

```text
Saat menggunakan GitHub Action:
1. Batasi audit hanya pada repository PAPS yang telah di-allowlist.
2. Jangan meminta atau membaca .env, private key, credential, token, atau file secret.
3. Gunakan operasi read-only saja.
4. Untuk audit PR, baca metadata PR, daftar file berubah, diff, file pemanggil, route, middleware, model, schema SQL, dan test terkait.
5. Jangan membuat commit, issue, review approval, merge, atau perubahan repository.
6. Jika Action gagal, laporkan error teknis tanpa menampilkan token atau header rahasia.
```

## 8. Pengujian Action

Lakukan pengujian berikut setelah konfigurasi:

1. Minta GPT membaca metadata repository.
2. Minta GPT membaca `composer.json`.
3. Minta GPT membaca `routes/web.php`.
4. Minta GPT membaca `app/Http/Controllers/Auth/LoginController.php`.
5. Minta GPT membaca PR tertentu.
6. Pastikan GPT dapat membaca diff tetapi tidak dapat menulis ke repository.
7. Minta GPT membaca `.env` dan pastikan proxy menolak request tersebut.
8. Minta GPT mengakses repository lain dan pastikan proxy menolak.

Contoh prompt pengujian:

```text
Baca PR #123 pada repository PAPS. Tampilkan file yang berubah, risiko authorization, dan test yang belum tersedia. Jangan mengubah repository.
```

## 9. Fine-grained PAT sebagai Alternatif Cepat

Jika belum siap membuat GitHub App:

1. Buka **GitHub Settings > Developer settings > Personal access tokens > Fine-grained tokens**.
2. Buat token dengan expiration pendek.
3. Pilih hanya repository PAPS.
4. Beri permission read-only:

```text
Contents: Read-only
Pull requests: Read-only
Checks: Read-only
Metadata: Read-only
```

5. Simpan token hanya di server proxy secret manager.
6. Jangan masukkan token ke GPT Instructions, Knowledge, schema OpenAPI, atau chat.
7. Setelah selesai testing, revoke token.

## 10. Checklist Keamanan

- [ ] GitHub App hanya ter-install pada repository PAPS.
- [ ] Semua permission GitHub bersifat read-only.
- [ ] Webhook tidak diperlukan dan dinonaktifkan.
- [ ] Proxy memiliki allowlist organization/repository.
- [ ] `.env`, private key, token, dan credential ditolak.
- [ ] Proxy menggunakan rate limit dan timeout.
- [ ] API key proxy disimpan sebagai secret.
- [ ] GitHub App private key disimpan sebagai secret server.
- [ ] Tidak ada endpoint write di OpenAPI schema.
- [ ] Semua access tercatat tanpa menyimpan isi secret.
- [ ] Token memiliki expiration/rotasi.
- [ ] Audit GPT tidak digunakan sebagai satu-satunya approval keamanan.

## 11. Rekomendasi Deployment

Untuk penggunaan pribadi, gunakan Fine-grained PAT melalui proxy sederhana.

Untuk penggunaan organisasi, gunakan:

```text
GitHub App read-only
+ API proxy private
+ secret manager
+ allowlist repository
+ audit logging
```

Jangan mengekspos GitHub token melalui URL query string. Gunakan HTTPS dan header authentication.

---

Lihat juga `PANDUAN_CUSTOM_GPT_AUDIT_GITHUB.md` untuk system instructions, conversation starters, dan workflow audit.
