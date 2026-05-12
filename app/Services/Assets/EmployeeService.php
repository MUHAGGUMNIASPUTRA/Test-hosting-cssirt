<?php

// Tujuan: Handle CRUD pegawai dengan enkripsi field sensitif dan verifikasi reveal
// Caller: EmployeeController
// Side Effects: DB write

namespace App\Services\Assets;

use App\Models\Employee;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    private const SENSITIVE_FIELDS = ['nip', 'nik', 'phone', 'email'];

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): void
    {
        $updateData = array_filter(
            array_intersect_key($data, array_flip([
                'name', 'position_id', 'organization_id', 'year_joined', 'is_active',
            ])),
            fn ($v) => $v !== null
        );

        // Only update sensitive fields if a non-empty value is explicitly provided
        foreach (self::SENSITIVE_FIELDS as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $updateData[$field] = $data[$field];
            }
        }

        $employee->update($updateData);
    }

    /**
     * Verify admin password and return decrypted sensitive fields.
     *
     * @throws AuthorizationException
     */
    public function reveal(Employee $employee, string $password): array
    {
        if (! Hash::check($password, auth()->user()->password)) {
            throw new AuthorizationException('Password salah. Akses ditolak.');
        }

        return [
            'nip' => $employee->nip,
            'nik' => $employee->nik,
            'phone' => $employee->phone,
            'email' => $employee->email,
        ];
    }
}
