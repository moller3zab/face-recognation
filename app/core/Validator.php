<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $this->applyRule($field, $value, $rule, $params, $data);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule, array $params, array $allData): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || trim((string)$value) === '') {
                    $this->addError($field, "{$label} is required.");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$label} must be a valid email address.");
                }
                break;

            case 'min':
                $min = (int)($params[0] ?? 0);
                if (!empty($value) && strlen((string)$value) < $min) {
                    $this->addError($field, "{$label} must be at least {$min} characters.");
                }
                break;

            case 'max':
                $max = (int)($params[0] ?? 255);
                if (!empty($value) && strlen((string)$value) > $max) {
                    $this->addError($field, "{$label} may not exceed {$max} characters.");
                }
                break;

            case 'match':
                $otherField = $params[0] ?? '';
                if ($value !== ($allData[$otherField] ?? null)) {
                    $otherLabel = ucfirst(str_replace('_', ' ', $otherField));
                    $this->addError($field, "{$label} must match {$otherLabel}.");
                }
                break;

            case 'in':
                if (!empty($value) && !in_array((string)$value, $params, true)) {
                    $this->addError($field, "{$label} contains an invalid selection.");
                }
                break;

            case 'date':
                if (!empty($value) && strtotime((string)$value) === false) {
                    $this->addError($field, "{$label} is not a valid date format.");
                }
                break;

            case 'unique':
                // Format: unique:table,column,exceptId
                if (!empty($value)) {
                    $table = $params[0] ?? '';
                    $column = $params[1] ?? $field;
                    $exceptId = $params[2] ?? null;

                    $pdo = Database::getInstance()->getConnection();
                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val AND `deleted_at` IS NULL";
                    if ($exceptId !== null) {
                        $sql .= " AND `id` != :exceptId";
                    }

                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':val', $value);
                    if ($exceptId !== null) {
                        $stmt->bindValue(':exceptId', $exceptId);
                    }
                    $stmt->execute();

                    if ((int)$stmt->fetchColumn() > 0) {
                        $this->addError($field, "{$label} has already been taken.");
                    }
                }
                break;
        }
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstErrors(): array
    {
        $first = [];
        foreach ($this->errors as $field => $messages) {
            $first[$field] = $messages[0] ?? '';
        }
        return $first;
    }
}
