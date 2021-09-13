<?php

declare (strict_types=1);

namespace App\Model;

use App\Interfaces\SanityCheckInterface;

final class SanityCheckResult
{
    public SanityCheckInterface $entity;

    public array $errors;

    public function __construct(SanityCheckInterface $entity, array $errors = [])
    {
        $this->entity = $entity;
        $this->errors = $errors;
    }

    public function getEntity(): SanityCheckInterface
    {
        return $this->entity;
    }

    public function setEntity(SanityCheckInterface $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return array<\Error>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function addError(\Error $error): void
    {
        if (!in_array($error, $this->errors)) {
            $this->errors[] = $error;
        }
    }

    public function removeError(\Error $error): void
    {
        if (($key = array_search($error, $this->errors)) !== false) {
            unset($this->errors[$key]);
        }
    }

    public function allGood(): bool
    {
        return count($this->errors) === 0;
    }
}