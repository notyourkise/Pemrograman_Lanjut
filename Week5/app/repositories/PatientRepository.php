<?php

class PatientRepository
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function search(string $q = '', string $sort = 'id', string $dir = 'DESC', int $limit = 10, int $offset = 0): array
    {
        $allowedSort = ['id', 'name', 'dob'];
        if (!in_array($sort, $allowedSort, true)) { $sort = 'id'; }
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $rows = [];
        if ($q !== '') {
            $sql = "SELECT id, name, gender, dob, phone FROM patients WHERE deleted_at IS NULL AND name LIKE ? ORDER BY $sort $dir LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $like = '%' . $q . '%';
            $stmt->bind_param('sii', $like, $limit, $offset);
        } else {
            $sql = "SELECT id, name, gender, dob, phone FROM patients WHERE deleted_at IS NULL ORDER BY $sort $dir LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ii', $limit, $offset);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public function count(string $q = ''): int
    {
        if ($q !== '') {
            $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM patients WHERE deleted_at IS NULL AND name LIKE ?');
            $like = '%' . $q . '%';
            $stmt->bind_param('s', $like);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) AS c FROM patients WHERE deleted_at IS NULL');
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $c = (int) ($res->fetch_assoc()['c'] ?? 0);
        $stmt->close();
        return $c;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM patients WHERE id = ? AND deleted_at IS NULL');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
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
        $id = (int)$stmt->insert_id;
        $stmt->close();
        return $id;
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
