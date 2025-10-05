<?php

class AppointmentsController extends Controller
{
    private AppointmentRepository $repo;
    private PatientRepository $patients;

    public function __construct()
    {
        $this->repo = new AppointmentRepository();
        $this->patients = new PatientRepository();
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $sort = $_GET['sort'] ?? 'a.id';
        $dir = $_GET['dir'] ?? 'DESC';
        $perPage = 10;

        $total = $this->repo->count($q);
        $p = new Paginator($total, $page, $perPage);
        $rows = $this->repo->search($q, $sort, $dir, $p->perPage, $p->offset());

        $this->view('appointments/index', [
            'appointments' => $rows,
            'page' => $p->page,
            'perPage' => $p->perPage,
            'total' => $total,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function create(): void
    {
        $errors = [];
        $old = ['patient_id' => '', 'schedule' => '', 'notes' => ''];
        $patients = $this->patients->search('', 'name', 'ASC', 1000, 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['patient_id'] = (int)($_POST['patient_id'] ?? 0);
            $old['schedule'] = trim($_POST['schedule'] ?? '');
            $old['notes'] = trim($_POST['notes'] ?? '');

            if ($old['patient_id'] <= 0) { $errors['patient_id'] = 'Pilih pasien.'; }
            if ($old['schedule'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $old['schedule'])) {
                $errors['schedule'] = 'Format jadwal harus YYYY-MM-DDTHH:MM';
            } else {
                $old['schedule'] = str_replace('T', ' ', $old['schedule']) . ':00';
            }

            if (empty($errors)) {
                $this->repo->create($old);
                $this->flash('success', 'Janji temu berhasil dibuat.');
                header('Location: ?c=appointments&a=index');
                exit;
            }
        }

        $this->view('appointments/create', compact('errors', 'old', 'patients'));
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { http_response_code(400); echo 'Invalid ID'; return; }
        $row = $this->repo->findById($id);
        if (!$row) { http_response_code(404); echo 'Appointment not found'; return; }

        $errors = [];
        $patients = $this->patients->search('', 'name', 'ASC', 1000, 0);
        $old = [
            'patient_id' => (int)$row['patient_id'],
            'schedule' => str_replace(' ', 'T', substr($row['schedule'], 0, 16)),
            'notes' => $row['notes'] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['patient_id'] = (int)($_POST['patient_id'] ?? $old['patient_id']);
            $old['schedule'] = trim($_POST['schedule'] ?? $old['schedule']);
            $old['notes'] = trim($_POST['notes'] ?? $old['notes']);

            if ($old['patient_id'] <= 0) { $errors['patient_id'] = 'Pilih pasien.'; }
            if ($old['schedule'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $old['schedule'])) {
                $errors['schedule'] = 'Format jadwal harus YYYY-MM-DDTHH:MM';
            } else {
                $save = $old;
                $save['schedule'] = str_replace('T', ' ', $old['schedule']) . ':00';
            }

            if (empty($errors)) {
                $this->repo->update($id, $save ?? $old);
                $this->flash('success', 'Janji temu berhasil diperbarui.');
                header('Location: ?c=appointments&a=index');
                exit;
            }
        }

        $this->view('appointments/edit', compact('errors', 'old', 'patients', 'id'));
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        if ($id <= 0) { http_response_code(400); echo 'Invalid ID'; return; }
        $ok = $this->repo->delete($id);
        if ($ok) { $this->flash('success', 'Janji temu berhasil dihapus.'); }
        else { $this->flash('error', 'Gagal menghapus janji temu.'); }
        header('Location: ?c=appointments&a=index');
        exit;
    }
}
