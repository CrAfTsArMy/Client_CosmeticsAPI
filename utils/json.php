<?php

class Json
{
    protected string $json;
    protected array $data;

    public function __construct(string $json)
    {
        $this->json = $json;
        $this->reset();
    }

    public function update(string $json): void
    {
        $this->json = $json;
        $this->reset();
    }

    public function reset(): void
    {
        $this->data = json_decode($this->json, true);
    }

    public function enter(string $key): bool
    {
        $temp = $this->data[$key];
        if (is_array($temp)) {
            $this->data = $temp;
            return true;
        }
        return false;
    }

    public function contains(string $key): bool
    {
        return array_key_exists($key, $this->data) || in_array($key, $this->data);
    }

    public function get(string $key, array $data = null): mixed
    {
        if ($data == null) {
            $data = $this->data;
        }
        return $data[$key];
    }

    public function set(string $key, mixed $data): void
    {
        $this->data[$key] = $data;
    }

    public function getAndEnter(string $key): mixed
    {
        $cache = $this->data;
        $this->enter($key);
        return $this->get($key, $cache);
    }

    public function getJsonArray(): array
    {
        return $this->data;
    }
}