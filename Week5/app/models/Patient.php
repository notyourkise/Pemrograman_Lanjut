<?php

class Patient
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(int $limit = 50, int $offset = 0, ?string $q = null): array
    {
        $rows = [];
        if ($q !== null && $q !== '') {
            $sql = 'SELECT id, name, gender, dob, phone FROM patients WHERE deleted_at IS NULL AND name LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?';
            $stmt = $this->db->prepare($sql);
            $like = '%' . $q . '%';
            $stmt->bind_param('sii', $like, $limit, $offset);
        } else {
            $sql = 'SELECT id, name, gender, dob, phone FROM patients WHERE deleted_at IS NULL ORDER BY id DESC LIMIT ? OFFSET ?';
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ii', $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    public function countAll(?string $q = null): int
    {
        if ($q !== null && $q !== '') {
            $sql = 'SELECT COUNT(*) AS c FROM patients WHERE deleted_at IS NULL AND name LIKE ?';
            $stmt = $this->db->prepare($sql);
            $like = '%' . $q . '%';
            $stmt->bind_param('s', $like);
        } else {
            $sql = 'SELECT COUNT(*) AS c FROM patients WHERE deleted_at IS NULL';
            $stmt = $this->db->prepare($sql);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $count = (int) ($result->fetch_assoc()['c'] ?? 0);
        $stmt->close();
        return $count;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM patients WHERE id = ? AND deleted_at IS NULL');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO patients (name, gender, dob, phone, address) VALUES (?, ?, ?, ?, ?)');
        $name = $data['name'];
        $gender = $data['gender'];
        $dob = $data['dob'] ?: null;
        $phone = $data['phone'] ?: null;
        $address = $data['address'] ?? null;
        $stmt->bind_param('sssss', $name, $gender, $dob, $phone, $address);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return (int) $id;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE patients SET name = ?, gender = ?, dob = ?, phone = ?, address = ? WHERE id = ?');
        $name = $data['name'];
        $gender = $data['gender'];
        $dob = $data['dob'] ?: null;
        $phone = $data['phone'] ?: null;
        $address = $data['address'] ?? null;
        $stmt->bind_param('sssssi', $name, $gender, $dob, $phone, $address, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete(int $id, bool $soft = true): bool
    {
        if ($soft) {
            $stmt = $this->db->prepare('UPDATE patients SET deleted_at = NOW() WHERE id = ?');
            $stmt->bind_param('i', $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        }
        $stmt = $this->db->prepare('DELETE FROM patients WHERE id = ?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
