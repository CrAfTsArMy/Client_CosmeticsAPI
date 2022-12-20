<?php

class MySQL
{

    protected string $host;
    protected string $user;
    protected string $password;
    protected string $name;

    protected PDO $pdo;

    public function __construct(string $host = '127.0.0.1:3306', string $user = 'root', string $password = '', string $name = '')
    {
        $this->host = $host;
        $this->user = $user;
        $this->name = $name;
        $this->password = $password;
    }

    public function connect(): MySQL
    {
        try {
            $this->pdo = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->name, $this->user, $this->password);
            return $this;
        } catch (PDOException $th) {
            throw $th;
        }
    }

    public function disconnect(): MySQL
    {
        try {
            $this->pdo = null;
            return $this;
        } catch (PDOException $th) {
            throw $th;
        }
    }

    public function isConnected(): bool
    {
        try {
            return $this->pdo != null;
        } catch (PDOException $th) {
            throw $th;
        }
    }

    public function bind(string $host = '127.0.0.1:3308', string $user = 'user', string $password = '', string $name = ''): void
    {
        $this->disconnect();
        $this->host = $host;
        $this->user = $user;
        $this->name = $name;
        $this->password = $password;
        $this->connect();
    }

    public function query(string $query, array $values = [])
    {
        try {
            if ($this->isConnected()) {
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($values);
                return $stmt;
            }
        } catch (PDOException $e) {
            throw $e;
        }
        return null;
    }

    public function fetch(PDOStatement $stmt, int $mode = PDO::FETCH_ASSOC)
    {
        try {
            if ($stmt != null) {
                return $stmt->fetch($mode);
            }
        } catch (PDOException $e) {
            throw $e;
        }
        return null;
    }

    public function queryAndFetch(string $query, array $values = [], int $mode = PDO::FETCH_ASSOC)
    {
        try {
            return $this->fetch($this->query($query, $values), $mode);
        } catch (PDOException $e) {
            throw $e;
        }
        return null;
    }

    public function fetchAll(PDOStatement $stmt, int $mode = PDO::FETCH_ASSOC)
    {
        try {
            if ($stmt != null) {
                return $stmt->fetchAll($mode);
            }
        } catch (PDOException $e) {
            throw $e;
        }
        return null;
    }

    public function queryAndFetchAll(string $query, array $values = [], int $mode = PDO::FETCH_ASSOC)
    {
        try {
            return $this->fetchAll($this->query($query, $values), $mode);
        } catch (PDOException $e) {
            throw $e;
        }
        return null;
    }

    public function rowCount(PDOStatement $stmt): int
    {
        try {
            if ($stmt != null) {
                return $stmt->rowCount();
            }
        } catch (PDOException $e) {
            throw $e;
        }
        return 0;
    }

    public function queryAndRowCount(string $query, array $values = []): int
    {
        try {
            return $this->rowCount($this->query($query, $values));
        } catch (PDOException $e) {
            throw $e;
        }
        return 0;
    }

    public function update(string $update, array $values = []): void
    {
        try {
            if ($this->isConnected()) {
                $stmt = $this->pdo->prepare($update);
                $stmt->execute($values);
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }
}