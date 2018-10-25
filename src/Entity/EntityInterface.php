<?php

namespace App\Entity;

interface EntityInterface
{
    public function getType():string;
    public function getId(): ?int;
    public function toArray(bool $dataOnly): array;
    public function hydrate(array $data): void;
}
