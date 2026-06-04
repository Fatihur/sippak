# Role Access Penyesuaian Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menyesuaikan hak akses Admin/Operator, Kabid PPA, dan Kepala Dinas agar sesuai matriks Administrasi, Pengawasan, dan Monitoring/Evaluasi.

**Architecture:** Tambahkan helper akses terpusat di `App\Models\User`, lalu pakai helper tersebut di route dan Blade agar server-side authorization dan tampilan tombol konsisten. Kabid PPA dapat memberi catatan/tindak lanjut tanpa mengubah status resmi laporan; Kepala Dinas hanya monitoring/evaluasi.

**Tech Stack:** Laravel, PHP, Blade, PHPUnit/Pest-style Laravel tests existing via `php artisan test`, Vite/npm build.

---

## File Structure

- Modify: `app/Models/User.php` — menambahkan helper role/permission sederhana.
- Modify: `routes/web.php` — menyesuaikan middleware route cetak/export dan route catatan Kabid.
- Modify: `app/Http/Controllers/Admin/LaporanController.php` — menambahkan action catatan tindak lanjut Kabid.
- Modify: `resources/views/admin/laporan/show.blade.php` — menampilkan form catatan Kabid dan memakai helper permission untuk tombol operator.
- Modify: `resources/views/admin/rekap/index.blade.php` — menyesuaikan tombol cetak/export berdasarkan permission.
- Create/Modify tests if test suite structure supports it: `tests/Feature/RoleAccessTest.php` — verifikasi operator/kabid/kepala dinas.

---

### Task 1: Add Role Permission Helpers

**Files:**
- Modify: `app/Models/User.php`

- [ ] **Step 1: Add helper methods to `User`**

Add methods:

```php
public function isOperator(): bool
{
    return $this->role === self::ROLE_OPERATOR;
}

public function isKabidPpa(): bool
{
    return $this->role === self::ROLE_KEPALA_BIDANG;
}

public function isKepalaDinas(): bool
{
    return $this->role === self::ROLE_KEPALA_DINAS;
}

public function canManageLaporan(): bool
{
    return $this->isOperator();
}

public function canGiveTindakLanjut(): bool
{
    return $this->isKabidPpa();
}

public function canExportOfficialReport(): bool
{
    return $this->isOperator() || $this->isKepalaDinas();
}
```

- [ ] **Step 2: Run syntax check**

Run:

```bash
php -l app/Models/User.php
```

Expected: `No syntax errors detected in app/Models/User.php`.

---

### Task 2: Add Kabid Tindak Lanjut Action

**Files:**
- Modify: `app/Http/Controllers/Admin/LaporanController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Add controller method**

Add method to `LaporanController`:

```php
public function simpanTindakLanjutKabid(Request $request, Pengaduan $laporan): RedirectResponse
{
    abort_unless($request->user()?->canGiveTindakLanjut(), 403);

    $data = $request->validate([
        'catatan_tindak_lanjut' => ['required', 'string', 'max:2000'],
    ]);

    RiwayatStatusPengaduan::create([
        'pengaduan_id' => $laporan->id,
        'status' => $laporan->status,
        'catatan' => 'Catatan Kabid PPA: '.$data['catatan_tindak_lanjut'],
        'user_id' => $request->user()->id,
    ]);

    $this->logAktivitasService->catat('catatan_kabid_disimpan', 'Nomor tiket: '.$laporan->nomor_tiket);

    return back()->with('success', 'Catatan tindak lanjut Kabid PPA berhasil disimpan.');
}
```

- [ ] **Step 2: Add route with Kabid middleware**

Add inside admin group in `routes/web.php`:

```php
Route::post('/laporan/{laporan}/tindak-lanjut-kabid', [LaporanController::class, 'simpanTindakLanjutKabid'])->middleware('role:kepala_bidang')->name('laporan.tindak-lanjut-kabid');
```

- [ ] **Step 3: Run route/syntax check**

Run:

```bash
php -l app/Http/Controllers/Admin/LaporanController.php
php artisan route:list --name=admin.laporan.tindak-lanjut-kabid
```

Expected: no syntax errors and route appears.

---

### Task 3: Update Detail Laporan UI Conditions

**Files:**
- Modify: `resources/views/admin/laporan/show.blade.php`

- [ ] **Step 1: Replace direct operator checks with permission helper**

Use:

```php
@if(auth()->user()->canManageLaporan())
```

instead of:

```php
@if(auth()->user()->role === 'operator')
```

for edit/delete and operator action panels.

- [ ] **Step 2: Add Kabid tindak lanjut form**

In the aside after operator panels, add Kabid-only panel:

```blade
@elseif(auth()->user()->canGiveTindakLanjut())
    <section class="panel">
        <h3 class="panel-title">Catatan / Tindak Lanjut Kabid</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Berikan arahan pengawasan kepada petugas tanpa mengubah status resmi laporan.</p>
        <form method="POST" action="{{ route('admin.laporan.tindak-lanjut-kabid', $laporan) }}" class="mt-4 space-y-4">
            @csrf
            <div><label class="label">Catatan Tindak Lanjut</label><textarea name="catatan_tindak_lanjut" class="input" rows="4" required placeholder="Tulis arahan atau catatan untuk tindak lanjut petugas."></textarea></div>
            <button class="btn-primary w-full"><i class="fa-solid fa-comment-dots"></i> Simpan Catatan</button>
        </form>
    </section>
@else
```

- [ ] **Step 3: Run Blade smoke check**

Run:

```bash
php artisan view:clear
```

Expected: command succeeds.

---

### Task 4: Adjust Official Report Export Access

**Files:**
- Modify: `routes/web.php`
- Modify: `resources/views/admin/rekap/index.blade.php`

- [ ] **Step 1: Restrict official PDF export to operator and Kepala Dinas**

Change route:

```php
Route::get('/rekap/export-pdf', [RekapController::class, 'exportPdf'])->middleware('role:operator,kepala_dinas')->name('rekap.export-pdf');
```

- [ ] **Step 2: Ensure export/cetak button only shows to allowed users**

In `resources/views/admin/rekap/index.blade.php`, wrap PDF export button with:

```blade
@if(auth()->user()->canExportOfficialReport())
```

Keep CSV export operator-only.

- [ ] **Step 3: Run route check**

Run:

```bash
php artisan route:list --name=admin.rekap.export-pdf
```

Expected: route has `role:operator,kepala_dinas` middleware.

---

### Task 5: Verify Build and Tests

**Files:**
- No code files unless tests are added.

- [ ] **Step 1: Run PHP tests**

Run:

```bash
php artisan test
```

Expected: tests pass, or existing unrelated failures are documented.

- [ ] **Step 2: Run npm build as project workflow requires**

Run:

```bash
npm run build
```

Expected: build succeeds.

- [ ] **Step 3: Review git diff**

Run:

```bash
git diff -- app/Models/User.php routes/web.php app/Http/Controllers/Admin/LaporanController.php resources/views/admin/laporan/show.blade.php resources/views/admin/rekap/index.blade.php
```

Expected: diff only contains role-access changes.

---

## Self-Review

- Spec coverage: operator administration, Kabid monitoring plus tindak lanjut, Kepala Dinas monitoring/evaluation and official report access are covered.
- Placeholder scan: no TBD/TODO/fill-in-later instructions remain.
- Type consistency: helper method names are defined in `User` before Blade/routes rely on them.
