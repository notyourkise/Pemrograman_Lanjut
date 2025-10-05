<?php

class AppointmentRepository
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function search(string $q = '', string $sort = 'a.id', string $dir = 'DESC', int $limit = 10, int $offset = 0): array
    {
        $allowedSort = ['a.id', 'a.schedule', 'p.name'];
        if (!in_array($sort, $allowedSort, true)) { $sort = 'a.id'; }
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $rows = [];
        if ($q !== '') {
            $sql = "SELECT a.id, a.schedule, a.notes, p.name as patient_name
                    FROM appointments a
                    JOIN patients p ON p.id = a.patient_id
                    WHERE p.deleted_at IS NULL AND (p.name LIKE ? OR a.notes LIKE ?)
                    ORDER BY $sort $dir LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $like = '%' . $q . '%';
            $stmt->bind_param('ssii', $like, $like, $limit, $offset);
        } else {
            $sql = "SELECT a.id, a.schedule, a.notes, p.name as patient_name
                    FROM appointments a
                    JOIN patients p ON p.id = a.patient_id
                    WHERE p.deleted_at IS NULL
                    ORDER BY $sort $dir LIMIT ? OFFSET ?";
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
            $sql = 'SELECT COUNT(*) AS c FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE p.deleted_at IS NULL AND (p.name LIKE ? OR a.notes LIKE ?)';
            $stmt = $this->db->prepare($sql);
            $like = '%' . $q . '%';
            $stmt->bind_param('ss', $like, $like);
        } else {
            $sql = 'SELECT COUNT(*) AS c FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE p.deleted_at IS NULL';
            $stmt = $this->db->prepare($sql);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $c = (int) ($res->fetch_assoc()['c'] ?? 0);
        $stmt->close();
        return $c;
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT a.*, p.name as patient_name FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO appointments (patient_id, doctor_id, schedule, notes) VALUES (?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $patientId = (int) $data['patient_id'];
        $doctorId = !empty($data['doctor_id']) ? (int)$data['doctor_id'] : null;
        $schedule = $data['schedule'];
        $notes = $data['notes'] ?? null;
        $stmt->bind_param('iiss', $patientId, $doctorId, $schedule, $notes);
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE appointments SET patient_id = ?, doctor_id = ?, schedule = ?, notes = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $patientId = (int) $data['patient_id'];
        $doctorId = !empty($data['doctor_id']) ? (int)$data['doctor_id'] : null;
        $schedule = $data['schedule'];
        $notes = $data['notes'] ?? null;
        $stmt->bind_param('iissi', $patientId, $doctorId, $schedule, $notes, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM appointments WHERE id = ?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
