<?php

namespace App\DTO;

class UserDTO
{
    /**
     * UserDTO constructor.
     *
     * @param string $name
     * @param string $email
     * @param string $registration_number
     * @param string|null $password
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $registration_number,
        public readonly ?string $password = null
    ) {
    }

    /**
     * Criar DTO a partir de um array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            registration_number: $data['registration_number'],
            password: $data['password'] ?? null
        );
    }

    /**
     * Converter para array
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'registration_number' => $this->registration_number,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
