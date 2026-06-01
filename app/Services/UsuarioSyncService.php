<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsuarioSyncService
{
    public function sync(User $user): void
    {
        if (! Schema::hasTable('USUARIO')) {
            return;
        }

        $columns = collect(Schema::getColumnListing('USUARIO'));

        if ($columns->isEmpty()) {
            return;
        }

        $columnsByLower = $columns->mapWithKeys(fn (string $column) => [mb_strtolower($column) => $column]);
        $payload = [];

        $this->setFirstExisting($payload, $columnsByLower, ['id_user', 'user_id', 'id_usuario_fk'], $user->id);
        $this->setFirstExisting($payload, $columnsByLower, ['nombre', 'name', 'nombre_usuario', 'usuario'], $user->name);
        $this->setFirstExisting($payload, $columnsByLower, ['email', 'correo', 'correo_electronico'], $user->email);
        $this->setFirstExisting($payload, $columnsByLower, ['telefono', 'celular', 'phone'], $user->telefono);
        $this->setFirstExisting($payload, $columnsByLower, ['password', 'contrasena', 'clave', 'password_hash'], $user->password);
        $this->setFirstExisting($payload, $columnsByLower, ['activo', 'estado'], 1);

        $now = now();
        $this->setFirstExisting($payload, $columnsByLower, ['created_at', 'fecha_creacion'], $now);
        $this->setFirstExisting($payload, $columnsByLower, ['updated_at', 'fecha_actualizacion'], $now);

        $emailColumn = $this->firstExistingColumn($columnsByLower, ['email', 'correo', 'correo_electronico']);
        $linkColumn = $this->firstExistingColumn($columnsByLower, ['id_user', 'user_id', 'id_usuario_fk']);

        if ($emailColumn === null) {
            return;
        }

        $query = DB::table('USUARIO');
        if ($linkColumn !== null) {
            $query->where($linkColumn, $user->id);
        } else {
            $query->where($emailColumn, $user->email);
        }

        $existing = $query->first();

        if ($existing) {
            if (isset($payload['created_at'])) {
                unset($payload['created_at']);
            }
            if (isset($payload['fecha_creacion'])) {
                unset($payload['fecha_creacion']);
            }

            $updateQuery = DB::table('USUARIO');
            if ($linkColumn !== null) {
                $updateQuery->where($linkColumn, $user->id);
            } else {
                $updateQuery->where($emailColumn, $user->email);
            }

            $updateQuery->update($payload);
            return;
        }

        DB::table('USUARIO')->insert($payload);
    }

    /**
     * @param array<string, string> $columnsByLower
     * @param array<int, string> $candidates
     */
    private function firstExistingColumn(array $columnsByLower, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            $match = $columnsByLower[mb_strtolower($candidate)] ?? null;

            if ($match !== null) {
                return $match;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, string> $columnsByLower
     * @param array<int, string> $candidates
     */
    private function setFirstExisting(array &$payload, array $columnsByLower, array $candidates, mixed $value): void
    {
        $column = $this->firstExistingColumn($columnsByLower, $candidates);

        if ($column !== null) {
            $payload[$column] = $value;
        }
    }
}
