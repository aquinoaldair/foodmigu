<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Diner;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DinersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int $diningHallId;

    private int $createdCount = 0;

    private int $updatedCount = 0;

    public function __construct(int $diningHallId)
    {
        $this->diningHallId = $diningHallId;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $idCode = trim((string) ($row['id'] ?? $row['ID'] ?? $row[0] ?? ''));
            $name = trim((string) ($row['nombre'] ?? $row['Nombre'] ?? $row['name'] ?? $row['Name'] ?? $row[1] ?? ''));

            if ($idCode === '' && $name === '') {
                continue;
            }

            if ($idCode === '') {
                continue;
            }

            $diner = Diner::where('dining_hall_id', $this->diningHallId)
                ->where('id_code', $idCode)
                ->first();

            if ($diner) {
                $diner->update(['name' => $name ?: $diner->name]);
                $this->updatedCount++;
            } else {
                Diner::create([
                    'dining_hall_id' => $this->diningHallId,
                    'id_code' => $idCode,
                    'name' => $name ?: $idCode,
                ]);
                $this->createdCount++;
            }
        }
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }
}
