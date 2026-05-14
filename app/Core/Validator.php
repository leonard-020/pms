<?php

namespace App\Core;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $customMessages = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): bool
    {
        foreach ($this->rules as $field => $fieldRules) {
            $rules = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        // Parse rule with parameters: "min:3", "max:255"
        $params = [];
        if (str_contains($rule, ':')) {
            [$ruleName, $paramStr] = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
            $rule = $ruleName;
        }

        $label = $this->humanize($field);

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->addError($field, "{$label} is required.");
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$label} must be a valid email address.");
                }
                break;

            case 'min':
                $min = (int) ($params[0] ?? 0);
                if (is_string($value) && strlen($value) < $min) {
                    $this->addError($field, "{$label} must be at least {$min} characters.");
                } elseif (is_numeric($value) && $value < $min) {
                    $this->addError($field, "{$label} must be at least {$min}.");
                }
                break;

            case 'max':
                $max = (int) ($params[0] ?? 0);
                if (is_string($value) && strlen($value) > $max) {
                    $this->addError($field, "{$label} must not exceed {$max} characters.");
                } elseif (is_numeric($value) && $value > $max) {
                    $this->addError($field, "{$label} must not exceed {$max}.");
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, "{$label} must be a number.");
                }
                break;

            case 'date':
                if ($value !== null && $value !== '' && !strtotime($value)) {
                    $this->addError($field, "{$label} must be a valid date.");
                }
                break;

            case 'in':
                $allowed = $params;
                if ($value !== null && !in_array($value, $allowed, true)) {
                    $this->addError($field, "{$label} is invalid.");
                }
                break;

            case 'confirmed':
                $confirmation = $this->data["{$field}_confirmation"] ?? null;
                if ($value !== $confirmation) {
                    $this->addError($field, "{$label} confirmation does not match.");
                }
                break;

            case 'unique':
                // Handled at service level; skip here
                break;

            case 'nullable':
                // Always passes; skips further rules if null
                break;

            case 'alpha':
                if ($value !== null && $value !== '' && !preg_match('/^[\pL\s]+$/u', $value)) {
                    $this->addError($field, "{$label} may only contain letters and spaces.");
                }
                break;

            case 'alpha_num':
                if ($value !== null && $value !== '' && !preg_match('/^[\pL\pN\s]+$/u', $value)) {
                    $this->addError($field, "{$label} may only contain letters, numbers, and spaces.");
                }
                break;

            case 'phone':
                if ($value !== null && $value !== '' && !preg_match('/^[\d\s\+\-\(\)]{7,20}$/', $value)) {
                    $this->addError($field, "{$label} must be a valid phone number.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (isset($this->customMessages[$field])) {
            $message = $this->customMessages[$field];
        }
        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function customMessages(array $messages): self
    {
        $this->customMessages = $messages;
        return $this;
    }

    private function humanize(string $field): string
    {
        return str_replace(['_', '-'], ' ', ucfirst($field));
    }
}