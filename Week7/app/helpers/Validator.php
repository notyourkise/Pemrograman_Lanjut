<?php
/**
 * Server-Side Validation Helper
 * 
 * Class untuk validasi input dengan aturan yang dapat di-chain
 */
class Validator
{
    private array $errors = [];
    private mysqli $db;
    
    public function __construct(?mysqli $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }
    
    /**
     * Validasi field wajib diisi
     */
    public function required(string $field, mixed $value, string $label): self
    {
        if (empty($value) && $value !== '0' && $value !== 0) {
            $this->errors[$field] = "$label wajib diisi.";
        }
        return $this;
    }
    
    /**
     * Validasi panjang minimum
     */
    public function minLength(string $field, string $value, int $min, string $label): self
    {
        if (!empty($value) && mb_strlen($value) < $min) {
            $this->errors[$field] = "$label minimal $min karakter.";
        }
        return $this;
    }
    
    /**
     * Validasi panjang maksimum
     */
    public function maxLength(string $field, string $value, int $max, string $label): self
    {
        if (!empty($value) && mb_strlen($value) > $max) {
            $this->errors[$field] = "$label maksimal $max karakter.";
        }
        return $this;
    }
    
    /**
     * Validasi format email
     */
    public function email(string $field, string $value, string $label): self
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label harus berformat email yang valid.";
        }
        return $this;
    }
    
    /**
     * Validasi hanya angka
     */
    public function numeric(string $field, mixed $value, string $label): self
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field] = "$label harus berupa angka.";
        }
        return $this;
    }
    
    /**
     * Validasi format tanggal YYYY-MM-DD
     */
    public function date(string $field, string $value, string $label): self
    {
        if (!empty($value) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $this->errors[$field] = "$label harus berformat YYYY-MM-DD.";
        } elseif (!empty($value)) {
            // Validasi tanggal valid
            $parts = explode('-', $value);
            if (count($parts) === 3) {
                if (!checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
                    $this->errors[$field] = "$label bukan tanggal yang valid.";
                }
            }
        }
        return $this;
    }
    
    /**
     * Validasi format datetime YYYY-MM-DD HH:MM:SS
     */
    public function datetime(string $field, string $value, string $label): self
    {
        if (!empty($value) && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
            $this->errors[$field] = "$label harus berformat YYYY-MM-DD HH:MM:SS.";
        }
        return $this;
    }
    
    /**
     * Validasi tanggal tidak boleh di masa depan
     */
    public function notFutureDate(string $field, string $value, string $label): self
    {
        if (!empty($value) && strtotime($value) > time()) {
            $this->errors[$field] = "$label tidak boleh di masa depan.";
        }
        return $this;
    }
    
    /**
     * Validasi tanggal tidak boleh di masa lalu
     */
    public function notPastDate(string $field, string $value, string $label): self
    {
        if (!empty($value) && strtotime($value) < strtotime('today')) {
            $this->errors[$field] = "$label tidak boleh di masa lalu.";
        }
        return $this;
    }
    
    /**
     * Validasi dengan regex custom
     */
    public function regex(string $field, string $value, string $pattern, string $label, string $message = null): self
    {
        if (!empty($value) && !preg_match($pattern, $value)) {
            $this->errors[$field] = $message ?? "$label format tidak valid.";
        }
        return $this;
    }
    
    /**
     * Validasi nilai harus unik di database
     */
    public function unique(string $field, string $value, string $table, string $column, string $label, ?int $exceptId = null): self
    {
        if (empty($value)) {
            return $this;
        }
        
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
        if ($exceptId !== null) {
            $sql .= " AND id != ?";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($exceptId !== null) {
            $stmt->bind_param('si', $value, $exceptId);
        } else {
            $stmt->bind_param('s', $value);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row['count'] > 0) {
            $this->errors[$field] = "$label sudah terdaftar, gunakan yang lain.";
        }
        
        return $this;
    }
    
    /**
     * Validasi foreign key exists di tabel lain
     */
    public function exists(string $field, mixed $value, string $table, string $column, string $label): self
    {
        if (empty($value) || $value <= 0) {
            return $this;
        }
        
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $table WHERE $column = ?");
        $stmt->bind_param('i', $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row['count'] === 0) {
            $this->errors[$field] = "$label yang dipilih tidak valid.";
        }
        
        return $this;
    }
    
    /**
     * Validasi nilai berada dalam list yang diizinkan
     */
    public function in(string $field, mixed $value, array $allowed, string $label): self
    {
        if (!empty($value) && !in_array($value, $allowed, true)) {
            $this->errors[$field] = "$label harus salah satu dari: " . implode(', ', $allowed) . ".";
        }
        return $this;
    }
    
    /**
     * Validasi nilai minimum (untuk angka)
     */
    public function min(string $field, mixed $value, float $min, string $label): self
    {
        if (!empty($value) && is_numeric($value) && (float)$value < $min) {
            $this->errors[$field] = "$label minimal $min.";
        }
        return $this;
    }
    
    /**
     * Validasi nilai maksimum (untuk angka)
     */
    public function max(string $field, mixed $value, float $max, string $label): self
    {
        if (!empty($value) && is_numeric($value) && (float)$value > $max) {
            $this->errors[$field] = "$label maksimal $max.";
        }
        return $this;
    }
    
    /**
     * Validasi nomor telepon (Indonesia)
     */
    public function phone(string $field, string $value, string $label): self
    {
        if (!empty($value) && !preg_match('/^[\d +\-\(\)]{8,20}$/', $value)) {
            $this->errors[$field] = "$label hanya boleh berisi angka, spasi, +, -, ( ) dengan panjang 8-20 karakter.";
        }
        return $this;
    }
    
    /**
     * Validasi file upload
     */
    public function file(string $field, array $file, array $allowedTypes, int $maxSize, string $label): self
    {
        if (empty($file['name'])) {
            return $this;
        }
        
        // Cek error upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$field] = "$label gagal diupload.";
            return $this;
        }
        
        // Cek ukuran
        if ($file['size'] > $maxSize) {
            $maxMB = round($maxSize / 1024 / 1024, 2);
            $this->errors[$field] = "$label maksimal $maxMB MB.";
            return $this;
        }
        
        // Cek tipe MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes, true)) {
            $this->errors[$field] = "$label tipe file tidak diizinkan.";
        }
        
        return $this;
    }
    
    /**
     * Cek apakah ada error
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Cek apakah validasi berhasil
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }
    
    /**
     * Ambil semua error
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Ambil error untuk field tertentu
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
    
    /**
     * Set custom error message
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }
}
