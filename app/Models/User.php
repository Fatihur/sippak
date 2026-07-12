<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'nip', 'jabatan', 'aktif', 'terakhir_login_at'])]
#[Hidden(['password', 'remember_token'])]
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'aktif' => 'boolean',
            'terakhir_login_at' => 'datetime',
        ];
    }
}
