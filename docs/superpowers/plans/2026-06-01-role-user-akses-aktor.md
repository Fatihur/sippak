# Role User Akses Aktor Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menyesuaikan hak akses dan label role user admin sesuai empat aktor: Admin/Operator, Kabid PPA, Kepala Dinas P2KBP3A, dan Pelapor tanpa login.

**Architecture:** Role internal tetap memakai nilai database yang sudah ada (`operator`, `kepala_bidang`, `kepala_dinas`) supaya tidak perlu migrasi data. Tambahkan satu sumber kebenaran label role di `App\Models\User`, lalu pakai di controller/view dan uji akses route agar aksi operasional hanya bisa dilakukan Admin/Operator. Pelapor tetap memakai alur publik pengaduan/tracking tanpa akun login.

**Tech Stack:** Laravel 13, PHP 8.3, Blade, PHPUnit Feature Tests, Vite/Tailwind build.

---

## File Structure

- Modify: `app/Models/User.php`
  - Tambahkan konstanta daftar role internal.
  - Tambahkan helper `roleLabel()` dan `opsiRole()` agar label konsisten.
- Modify: `app/Http/Controllers/Admin/DashboardController.php`
  - Rapikan label dashboard Kepala Dinas menjadi `Dashboard Kepala Dinas P2KBP3A`.
- Modify: `resources/views/admin/pengguna/form.blade.php`
  - Gunakan `User::opsiRole()` untuk dropdown role.
- Modify: `resources/views/admin/pengguna/index.blade.php`
  - Gunakan `roleLabel()` untuk tabel dan ubah subtitle sesuai aktor.
- Modify: `resources/views/layouts/tailadmin-header.blade.php`
  - Gunakan `roleLabel()` di header user.
  - Tampilkan notifikasi admin hanya untuk `operator` agar role monitoring tidak melihat notifikasi operasional.
- Modify: `resources/views/layouts/tailadmin-sidebar.blade.php`
  - Sesuaikan label panel dan menu sesuai peran.
- Modify: `resources/views/admin/rekap/index.blade.php`
  - `Export CSV` dan `Backup Database` tetap hanya untuk operator; PDF/cetak tetap bisa untuk monitoring.
- Modify: `tests/Feature/SippakFeatureTest.php`
  - Tambahkan test role label.
  - Tambahkan test Kabid dan Kepala Dinas bisa monitoring read-only.
  - Tambahkan test Kabid dan Kepala Dinas tidak bisa mutasi data operasional.
  - Tambahkan test role `pelapor` tidak bisa login admin.

## Task 1: Tambahkan Test Role Label dan Akses Aktor

**Files:**
- Modify: `tests/Feature/SippakFeatureTest.php`

- [ ] **Step 1: Tambahkan test failing untuk label role dan akses read-only**

Tambahkan method berikut setelah `test_operator_dapat_mengubah_status_dan_mencatat_riwayat()`:

```php
    public function test_label_role_user_sesuai_aktor_internal(): void
    {
        $this->assertSame('Admin/Operator', User::roleLabel('operator'));
        $this->assertSame('Kabid PPA', User::roleLabel('kepala_bidang'));
        $this->assertSame('Kepala Dinas P2KBP3A', User::roleLabel('kepala_dinas'));
        $this->assertSame('Role Tidak Dikenal', User::roleLabel('pelapor'));
        $this->assertSame([
            'operator' => 'Admin/Operator',
            'kepala_bidang' => 'Kabid PPA',
            'kepala_dinas' => 'Kepala Dinas P2KBP3A',
        ], User::opsiRole());
    }

    public function test_kabid_dan_kepala_dinas_dapat_memonitor_laporan_tanpa_aksi_operasional(): void
    {
        $pengaduan = Pengaduan::create($this->dataPengaduan([
            'nomor_tiket' => 'PPA-2026-0003',
            'status' => 'diterima',
        ]));

        foreach (['kepala_bidang', 'kepala_dinas'] as $role) {
            $user = User::factory()->create(['role' => $role, 'aktif' => true]);

            $this->actingAs($user)
                ->get(route('admin.dashboard'))
                ->assertOk();

            $this->actingAs($user)
                ->get(route('admin.laporan.index'))
                ->assertOk()
                ->assertSee($pengaduan->nomor_tiket)
                ->assertDontSee('Edit')
                ->assertDontSee('Hapus');

            $this->actingAs($user)
                ->get(route('admin.laporan.show', $pengaduan))
                ->assertOk()
                ->assertSee('Mode Monitoring')
                ->assertDontSee('Simpan Status')
                ->assertDontSee('Simpan Asesmen')
                ->assertDontSee('Kirim Panggilan');

            $this->actingAs($user)
                ->get(route('admin.rekap.index'))
                ->assertOk()
                ->assertSee('Export PDF / Cetak')
                ->assertDontSee('Export Excel/CSV')
                ->assertDontSee('Backup Database');
        }
    }
```

- [ ] **Step 2: Tambahkan test failing untuk blokir aksi mutasi role monitoring dan pelapor**

Tambahkan method berikut setelah test pada Step 1:

```php
    public function test_kabid_dan_kepala_dinas_tidak_dapat_melakukan_aksi_operasional(): void
    {
        $pengaduan = Pengaduan::create($this->dataPengaduan([
            'nomor_tiket' => 'PPA-2026-0004',
            'status' => 'menunggu_verifikasi',
        ]));

        foreach (['kepala_bidang', 'kepala_dinas'] as $role) {
            $user = User::factory()->create(['role' => $role, 'aktif' => true]);

            $this->actingAs($user)
                ->patch(route('admin.laporan.status', $pengaduan), [
                    'status' => 'diterima',
                    'tingkat_urgensi' => 'tinggi',
                    'catatan' => 'Tidak boleh berubah oleh role monitoring.',
                ])
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('admin.pengguna.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('admin.whatsapp.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('admin.backup.sqlite'))
                ->assertForbidden();
        }

        $pengaduan->refresh();
        $this->assertSame('menunggu_verifikasi', $pengaduan->status);
        $this->assertSame(0, $pengaduan->riwayatStatus()->count());
    }

    public function test_pelapor_tidak_memiliki_akses_login_admin(): void
    {
        $pelapor = User::factory()->create([
            'email' => 'pelapor-test@sippak.test',
            'password' => 'password',
            'role' => 'pelapor',
            'aktif' => true,
        ]);

        $this->post(route('login.proses'), [
            'email' => $pelapor->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
```

- [ ] **Step 3: Jalankan test untuk memastikan RED**

Run:

```bash
php artisan test --filter=SippakFeatureTest
```

Expected:
- FAIL karena `User::roleLabel()` belum ada.
- FAIL karena `User::opsiRole()` belum ada.
- FAIL untuk login pelapor karena login saat ini belum membatasi role petugas.

## Task 2: Implementasi Helper Role dan Pembatasan Login Petugas

**Files:**
- Modify: `app/Models/User.php`
- Modify: `app/Http/Controllers/AuthController.php`

- [ ] **Step 1: Tambahkan helper role ke `app/Models/User.php`**

Ubah class menjadi seperti ini dengan menambahkan konstanta dan method setelah `use HasFactory, Notifiable;`:

```php
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_OPERATOR = 'operator';
    public const ROLE_KEPALA_BIDANG = 'kepala_bidang';
    public const ROLE_KEPALA_DINAS = 'kepala_dinas';

    /**
     * @return array<string, string>
     */
    public static function opsiRole(): array
    {
        return [
            self::ROLE_OPERATOR => 'Admin/Operator',
            self::ROLE_KEPALA_BIDANG => 'Kabid PPA',
            self::ROLE_KEPALA_DINAS => 'Kepala Dinas P2KBP3A',
        ];
    }

    public static function roleLabel(?string $role): string
    {
        return self::opsiRole()[$role] ?? 'Role Tidak Dikenal';
    }

    public function labelRole(): string
    {
        return self::roleLabel($this->role);
    }
```

- [ ] **Step 2: Batasi login hanya untuk role petugas resmi di `app/Http/Controllers/AuthController.php`**

Tambahkan import:

```php
use App\Models\User;
```

Ganti blok login dari:

```php
        if (Auth::attempt($credentials + ['aktif' => true], $ingat)) {
            $request->session()->regenerate();
            $request->user()->update(['terakhir_login_at' => now()]);
            $this->logAktivitasService->catat('login', 'Petugas masuk ke sistem.');

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Berhasil masuk.');
        }
```

Menjadi:

```php
        if (Auth::attempt($credentials + ['aktif' => true], $ingat)) {
            $user = $request->user();

            if (! array_key_exists($user->role, User::opsiRole())) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'Email atau password tidak sesuai, atau akun tidak aktif.'])->onlyInput('email');
            }

            $request->session()->regenerate();
            $user->update(['terakhir_login_at' => now()]);
            $this->logAktivitasService->catat('login', 'Petugas masuk ke sistem.');

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Berhasil masuk.');
        }
```

- [ ] **Step 3: Jalankan test untuk memastikan GREEN helper dan login**

Run:

```bash
php artisan test --filter=SippakFeatureTest
```

Expected:
- Test role label dan pelapor login pass.
- Jika ada fail lain, fail tersebut harus terkait view text/hak akses tampilan dan dilanjutkan di task berikutnya.

## Task 3: Terapkan Label Role Konsisten di Dashboard, Header, Sidebar, dan Manajemen Pengguna

**Files:**
- Modify: `app/Http/Controllers/Admin/DashboardController.php`
- Modify: `resources/views/admin/pengguna/form.blade.php`
- Modify: `resources/views/admin/pengguna/index.blade.php`
- Modify: `resources/views/layouts/tailadmin-header.blade.php`
- Modify: `resources/views/layouts/tailadmin-sidebar.blade.php`

- [ ] **Step 1: Ubah label dashboard Kepala Dinas**

Di `app/Http/Controllers/Admin/DashboardController.php`, ganti:

```php
                'label' => 'Dashboard Kepala Dinas',
```

Menjadi:

```php
                'label' => 'Dashboard Kepala Dinas P2KBP3A',
```

- [ ] **Step 2: Gunakan `User::opsiRole()` di form pengguna**

Di `resources/views/admin/pengguna/form.blade.php`, ganti dropdown role satu baris:

```blade
        <div><label class="label">Role</label><select name="role" class="input">@foreach(['operator'=>'Operator/Admin','kepala_bidang'=>'Kepala Bidang','kepala_dinas'=>'Kepala Dinas'] as $key=>$label)<option value="{{ $key }}" @selected(old('role',$user->role ?: 'operator')===$key)>{{ $label }}</option>@endforeach</select></div>
```

Menjadi:

```blade
        <div><label class="label">Role</label><select name="role" class="input">@foreach(\App\Models\User::opsiRole() as $key=>$label)<option value="{{ $key }}" @selected(old('role',$user->role ?: \App\Models\User::ROLE_OPERATOR)===$key)>{{ $label }}</option>@endforeach</select></div>
```

- [ ] **Step 3: Gunakan label role di index pengguna**

Di `resources/views/admin/pengguna/index.blade.php`, ganti subtitle:

```blade
            <p class="panel-subtitle">Kelola petugas operator, kepala bidang, dan kepala dinas.</p>
```

Menjadi:

```blade
            <p class="panel-subtitle">Kelola akun Admin/Operator, Kabid PPA, dan Kepala Dinas P2KBP3A.</p>
```

Ganti cell role:

```blade
                            <td class="capitalize">{{ str_replace('_',' ',$user->role) }}</td>
```

Menjadi:

```blade
                            <td>{{ $user->labelRole() }}</td>
```

- [ ] **Step 4: Gunakan label role di header dan batasi notifikasi admin untuk operator**

Di `resources/views/layouts/tailadmin-header.blade.php`, ganti wrapper notifikasi:

```blade
            <div class="relative" x-data="{ open: false }" x-init="setInterval(() => { fetch('{{ route('admin.dashboard') }}', {headers:{'X-Requested-With':'XMLHttpRequest'}}).catch(() => {}) }, 60000)">
```

Menjadi:

```blade
            @if(auth()->user()->role === \App\Models\User::ROLE_OPERATOR)
            <div class="relative" x-data="{ open: false }" x-init="setInterval(() => { fetch('{{ route('admin.dashboard') }}', {headers:{'X-Requested-With':'XMLHttpRequest'}}).catch(() => {}) }, 60000)">
```

Tambahkan `@endif` setelah `</div>` penutup dropdown notifikasi dan sebelum `<div class="flex items-center gap-3">`.

Ganti role display:

```blade
                    <p class="text-xs capitalize text-gray-500 dark:text-gray-400">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
```

Menjadi:

```blade
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->labelRole() }}</p>
```

- [ ] **Step 5: Sesuaikan label panel dan menu sidebar**

Di `resources/views/layouts/tailadmin-sidebar.blade.php`, ganti:

```blade
                <span class="block text-xs text-gray-500 dark:text-gray-400">Admin Panel PPA</span>
```

Menjadi:

```blade
                <span class="block text-xs text-gray-500 dark:text-gray-400">Panel {{ auth()->user()->labelRole() }}</span>
```

Ganti label menu rekap:

```blade
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Rekap & Export</span>
```

Menjadi:

```blade
                                <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Rekap & Statistik</span>
```

- [ ] **Step 6: Jalankan test feature**

Run:

```bash
php artisan test --filter=SippakFeatureTest
```

Expected: PASS untuk semua test di `SippakFeatureTest`.

## Task 4: Pastikan Hak Akses Route Sesuai Aktor dan Build Sukses

**Files:**
- Verify only: `routes/web.php`
- Verify only: `resources/views/admin/laporan/index.blade.php`
- Verify only: `resources/views/admin/laporan/show.blade.php`
- Verify only: `resources/views/admin/rekap/index.blade.php`

- [ ] **Step 1: Verifikasi route read-only dan operator-only**

Run:

```bash
php artisan route:list --path=admin
```

Expected:
- `/admin/dashboard`, `/admin/laporan`, `/admin/laporan/{laporan}`, `/admin/rekap`, `/admin/rekap/export-pdf`, bukti preview/download dapat diakses role `operator,kepala_bidang,kepala_dinas` lewat group admin.
- Edit/update/delete/status/asesmen/panggil-kantor/pengguna/whatsapp/backup punya middleware `role:operator`.

- [ ] **Step 2: Jalankan seluruh test Laravel**

Run:

```bash
php artisan test
```

Expected: PASS semua test.

- [ ] **Step 3: Jalankan build frontend sesuai workflow project**

Run:

```bash
npm run build
```

Expected: Build sukses tanpa error.

- [ ] **Step 4: Cek diff akhir**

Run:

```bash
git diff -- app/Models/User.php app/Http/Controllers/AuthController.php app/Http/Controllers/Admin/DashboardController.php resources/views/admin/pengguna/form.blade.php resources/views/admin/pengguna/index.blade.php resources/views/layouts/tailadmin-header.blade.php resources/views/layouts/tailadmin-sidebar.blade.php tests/Feature/SippakFeatureTest.php
```

Expected:
- Tidak ada role `pelapor` ditambahkan ke dropdown user admin.
- Tidak ada migrasi baru.
- Mutasi data operasional tetap khusus operator.

## Self-Review

- Spec coverage: Plan mencakup label aktor, pembatasan hak akses, pelapor tanpa login, dan validasi build.
- Placeholder scan: Tidak ada TBD/TODO/implement later.
- Type consistency: Helper yang dipakai (`roleLabel`, `opsiRole`, `labelRole`) didefinisikan di Task 2 sebelum dipakai di Task 3.
