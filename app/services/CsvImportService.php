<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;

class CsvImportService
{
    private UserRepositoryInterface $userRepo;

    public function __construct(?UserRepositoryInterface $userRepo = null)
    {
        $this->userRepo = $userRepo ?? new UserRepository();
    }

    public function importUsersFromCsv(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return ['imported' => 0, 'errors' => ['Unable to read uploaded CSV file.']];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ['imported' => 0, 'errors' => ['Failed to open CSV file handle.']];
        }

        // Read header row
        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            fclose($handle);
            return ['imported' => 0, 'errors' => ['CSV file is empty.']];
        }

        // Normalize header column indices
        $columns = array_map(fn($col) => strtolower(trim((string)$col)), $header);
        $nameIdx = array_search('name', $columns, true);
        $emailIdx = array_search('email', $columns, true);
        $identifierIdx = array_search('identifier', $columns, true);
        $deptIdx = array_search('department', $columns, true);
        $phoneIdx = array_search('phone', $columns, true);

        if ($nameIdx === false || $emailIdx === false || $identifierIdx === false) {
            fclose($handle);
            return ['imported' => 0, 'errors' => ['CSV header must contain "name", "email", and "identifier" columns.']];
        }

        $imported = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;
            $name = trim($row[$nameIdx] ?? '');
            $email = trim($row[$emailIdx] ?? '');
            $identifier = trim($row[$identifierIdx] ?? '');
            $department = $deptIdx !== false ? trim($row[$deptIdx] ?? '') : null;
            $phone = $phoneIdx !== false ? trim($row[$phoneIdx] ?? '') : null;

            // Row-level validation
            if (empty($name) || empty($email) || empty($identifier)) {
                $errors[] = "Row #{$rowNum}: Missing required field (name, email, or identifier).";
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row #{$rowNum}: Invalid email format ({$email}).";
                continue;
            }

            if ($this->userRepo->findByEmail($email)) {
                $errors[] = "Row #{$rowNum}: Duplicate email ({$email}) already exists in system.";
                continue;
            }

            if ($this->userRepo->findByIdentifier($identifier)) {
                $errors[] = "Row #{$rowNum}: Duplicate identifier ({$identifier}) already exists in system.";
                continue;
            }

            $this->userRepo->create([
                'name' => $name,
                'email' => $email,
                'identifier' => $identifier,
                // Imported users remain inactive until an administrator verifies the
                // record, sets a private initial password, and activates the account.
                'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT),
                'role' => 'user',
                'department' => $department,
                'phone' => $phone,
                'status' => 'inactive'
            ]);

            $imported++;
        }

        fclose($handle);

        return [
            'imported' => $imported,
            'errors' => $errors
        ];
    }
}
