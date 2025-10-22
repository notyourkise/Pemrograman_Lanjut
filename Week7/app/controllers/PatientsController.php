<?php
/**
 * Patients Controller - Week 7 (dengan CSRF Protection & Validation)
 * 
 * Controller untuk mengelola data pasien dengan keamanan yang ditingkatkan
 */
class PatientsController extends Controller
{
    private PatientRepository $repo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->repo = new PatientRepository();
    }

    /**
     * Menampilkan daftar pasien dengan pencarian, sorting, dan pagination
     */
    public function index(): void
    {
        // Sanitasi dan validasi input query string
        $page = max(1, Sanitizer::int($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        // Whitelist untuk kolom sorting (keamanan)
        $allowedSort = ['id', 'name', 'gender', 'dob', 'created_at'];
        $sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'id';
        
        // Validasi direction
        $dir = strtoupper($_GET['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        
        $perPage = 10;
        $total = $this->repo->count($q);
        $p = new Paginator($total, $page, $perPage);
        $rows = $this->repo->search($q, $sort, $dir, $p->perPage, $p->offset());

        $this->view('patients/index', [
            'patients' => $rows,
            'page' => $p->page,
            'perPage' => $p->perPage,
            'total' => $total,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir
        ]);
    }

    /**
     * Menampilkan form tambah pasien
     */
    public function create(): void
    {
        $errors = [];
        $old = [
            'name' => '',
            'gender' => 'M',
            'dob' => '',
            'phone' => '',
            'address' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verifikasi CSRF token
            Csrf::verifyOrFail($_POST['csrf_token'] ?? null);

            // Ambil dan sanitasi input
            $input = Sanitizer::inputs($_POST, ['name', 'gender', 'dob', 'phone', 'address']);
            $old['name'] = $input['name'];
            $old['gender'] = $input['gender'] === 'F' ? 'F' : 'M';
            $old['dob'] = $input['dob'];
            $old['phone'] = $input['phone'];
            $old['address'] = $input['address'];

            // Validasi menggunakan Validator class
            $validator = new Validator();
            $validator
                ->required('name', $old['name'], 'Nama')
                ->minLength('name', $old['name'], 3, 'Nama')
                ->maxLength('name', $old['name'], 100, 'Nama')
                ->in('gender', $old['gender'], ['M', 'F'], 'Gender')
                ->date('dob', $old['dob'], 'Tanggal Lahir')
                ->notFutureDate('dob', $old['dob'], 'Tanggal Lahir')
                ->phone('phone', $old['phone'], 'Telepon')
                ->unique('phone', $old['phone'], 'patients', 'phone', 'Nomor Telepon')
                ->maxLength('address', $old['address'], 500, 'Alamat');

            $errors = $validator->getErrors();

            // Jika tidak ada error, simpan ke database
            if (empty($errors)) {
                $this->repo->create($old);
                $this->flash('success', 'Pasien berhasil ditambahkan.');
                
                // Regenerate CSRF token untuk keamanan
                Csrf::regenerate();
                
                header('Location: ?c=patients&a=index');
                exit;
            }
        }

        $this->view('patients/create', compact('errors', 'old'));
    }

    /**
     * Menampilkan form edit pasien
     */
    public function edit(): void
    {
        $id = Sanitizer::int($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid ID';
            return;
        }

        $row = $this->repo->findById($id);
        
        if (!$row) {
            http_response_code(404);
            echo 'Pasien tidak ditemukan';
            return;
        }

        $errors = [];
        $old = [
            'name' => $row['name'],
            'gender' => $row['gender'],
            'dob' => $row['dob'] ?? '',
            'phone' => $row['phone'] ?? '',
            'address' => $row['address'] ?? ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verifikasi CSRF token
            Csrf::verifyOrFail($_POST['csrf_token'] ?? null);

            // Ambil dan sanitasi input
            $input = Sanitizer::inputs($_POST, ['name', 'gender', 'dob', 'phone', 'address']);
            $old['name'] = $input['name'];
            $old['gender'] = $input['gender'] === 'F' ? 'F' : 'M';
            $old['dob'] = $input['dob'];
            $old['phone'] = $input['phone'];
            $old['address'] = $input['address'];

            // Validasi menggunakan Validator class
            $validator = new Validator();
            $validator
                ->required('name', $old['name'], 'Nama')
                ->minLength('name', $old['name'], 3, 'Nama')
                ->maxLength('name', $old['name'], 100, 'Nama')
                ->in('gender', $old['gender'], ['M', 'F'], 'Gender')
                ->date('dob', $old['dob'], 'Tanggal Lahir')
                ->notFutureDate('dob', $old['dob'], 'Tanggal Lahir')
                ->phone('phone', $old['phone'], 'Telepon')
                ->unique('phone', $old['phone'], 'patients', 'phone', 'Nomor Telepon', $id)
                ->maxLength('address', $old['address'], 500, 'Alamat');

            $errors = $validator->getErrors();

            // Jika tidak ada error, update ke database
            if (empty($errors)) {
                $this->repo->update($id, $old);
                $this->flash('success', 'Pasien berhasil diperbarui.');
                
                // Regenerate CSRF token
                Csrf::regenerate();
                
                header('Location: ?c=patients&a=index');
                exit;
            }
        }

        $this->view('patients/edit', compact('errors', 'old', 'id'));
    }

    /**
     * Menghapus pasien (soft delete)
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        // Verifikasi CSRF token
        Csrf::verifyOrFail($_POST['csrf_token'] ?? null);

        $id = Sanitizer::int($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid ID';
            return;
        }

        $ok = $this->repo->delete($id, true); // soft delete
        
        $this->flash(
            $ok ? 'success' : 'error',
            $ok ? 'Pasien berhasil dihapus.' : 'Gagal menghapus pasien.'
        );

        // Regenerate CSRF token
        Csrf::regenerate();

        header('Location: ?c=patients&a=index');
        exit;
    }

    /**
     * Menampilkan Recycle Bin (pasien yang dihapus)
     */
    public function recycle(): void
    {
        $page = max(1, Sanitizer::int($_GET['page'] ?? 1));
        $perPage = 10;
        
        $total = $this->repo->countDeleted();
        $p = new Paginator($total, $page, $perPage);
        $rows = $this->repo->getDeleted($p->perPage, $p->offset());

        $this->view('patients/recycle', [
            'patients' => $rows,
            'page' => $p->page,
            'perPage' => $p->perPage,
            'total' => $total
        ]);
    }

    /**
     * Restore pasien dari recycle bin
     */
    public function restore(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        // Verifikasi CSRF token
        Csrf::verifyOrFail($_POST['csrf_token'] ?? null);

        $id = Sanitizer::int($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid ID';
            return;
        }

        $ok = $this->repo->restore($id);
        
        $this->flash(
            $ok ? 'success' : 'error',
            $ok ? 'Pasien berhasil dipulihkan.' : 'Gagal memulihkan pasien.'
        );

        // Regenerate CSRF token
        Csrf::regenerate();

        header('Location: ?c=patients&a=recycle');
        exit;
    }

    /**
     * Hapus permanen dari recycle bin
     */
    public function forceDelete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        // Verifikasi CSRF token
        Csrf::verifyOrFail($_POST['csrf_token'] ?? null);

        $id = Sanitizer::int($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid ID';
            return;
        }

        $ok = $this->repo->delete($id, false); // hard delete
        
        $this->flash(
            $ok ? 'success' : 'error',
            $ok ? 'Pasien berhasil dihapus permanen.' : 'Gagal menghapus pasien.'
        );

        // Regenerate CSRF token
        Csrf::regenerate();

        header('Location: ?c=patients&a=recycle');
        exit;
    }
}
