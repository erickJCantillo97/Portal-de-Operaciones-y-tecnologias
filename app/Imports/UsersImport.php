<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name' => $row['nombre'],
            'manager' => $row['responsable'],
            'executor' => $row['ejecutor'],
        ]);
    }

    public function batchSize(): int
    {
        return 4000;
    }

    public function chunkSize(): int
    {
        return 4000;
    }

    public function rules(): array
    {
        return [
            '*.name' => [
                'string',
                'required',
            ],
            '*.manager' => [
                'string',
                'required',
            ],
            '*.executor' => [
                'string',
                'required',
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name' => 'El campo Nombre debe ser de tipo String.',
            'manager' => 'El campo Responsable debe ser de tipo String.',
            'executor' => 'El campo Ejecutor debe ser de tipo String.',
        ];
    }
}
