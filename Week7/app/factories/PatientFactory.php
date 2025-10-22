<?php
/**
 * Patient Factory - Factory Pattern Implementation
 * 
 * Class untuk generate dummy data pasien dengan aturan tertentu
 * Design Pattern: Factory Pattern
 */
class PatientFactory
{
    /**
     * Daftar nama depan berdasarkan gender
     */
    private static array $firstNames = [
        'M' => ['Ahmad', 'Budi', 'Dedi', 'Eko', 'Fajar', 'Gilang', 'Hadi', 'Irfan', 'Joko', 'Rudi'],
        'F' => ['Ani', 'Dewi', 'Fitri', 'Indah', 'Sari', 'Lila', 'Maya', 'Nina', 'Putri', 'Rina']
    ];
    
    /**
     * Daftar nama belakang
     */
    private static array $lastNames = [
        'Pratama', 'Santoso', 'Wijaya', 'Kurniawan', 'Lestari',
        'Saputra', 'Nugraha', 'Permana', 'Hidayat', 'Raharjo'
    ];
    
    /**
     * Daftar nama jalan
     */
    private static array $streets = [
        'Merdeka', 'Sudirman', 'Thamrin', 'Gatot Subroto', 'Ahmad Yani',
        'Diponegoro', 'Imam Bonjol', 'Veteran', 'Pahlawan', 'Pemuda'
    ];
    
    /**
     * Daftar nama kota
     */
    private static array $cities = [
        'Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang',
        'Yogyakarta', 'Malang', 'Denpasar', 'Makassar', 'Palembang'
    ];
    
    /**
     * Generate 1 data pasien dummy
     * 
     * @param array $overrides Data yang ingin di-override
     * @return array Data pasien
     */
    public static function create(array $overrides = []): array
    {
        // Generate gender (default random)
        $gender = $overrides['gender'] ?? (rand(0, 1) ? 'M' : 'F');
        
        // Generate nama berdasarkan gender
        $firstName = self::$firstNames[$gender][array_rand(self::$firstNames[$gender])];
        $lastName = self::$lastNames[array_rand(self::$lastNames)];
        
        // Generate tanggal lahir (18-70 tahun yang lalu)
        $yearsAgo = rand(18, 70);
        $daysAgo = rand(0, 365);
        $dob = date('Y-m-d', strtotime("-{$yearsAgo} years -{$daysAgo} days"));
        
        // Generate nomor telepon (format Indonesia)
        $phone = '+62 8' . rand(10, 99) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999);
        
        // Generate alamat
        $street = self::$streets[array_rand(self::$streets)];
        $number = rand(1, 200);
        $city = self::$cities[array_rand(self::$cities)];
        $address = "Jl. {$street} No. {$number}, {$city}";
        
        // Merge dengan default values
        $defaults = [
            'name' => $firstName . ' ' . $lastName,
            'gender' => $gender,
            'dob' => $dob,
            'phone' => $phone,
            'address' => $address
        ];
        
        // Override dengan data yang diberikan
        return array_merge($defaults, $overrides);
    }
    
    /**
     * Generate banyak data pasien sekaligus
     * 
     * @param int $count Jumlah data yang akan di-generate
     * @return array Array of patients
     */
    public static function createMany(int $count): array
    {
        $patients = [];
        for ($i = 0; $i < $count; $i++) {
            $patients[] = self::create();
        }
        return $patients;
    }
    
    /**
     * Generate pasien dengan umur tertentu
     * 
     * @param int $age Umur pasien
     * @return array Data pasien
     */
    public static function createWithAge(int $age): array
    {
        $dob = date('Y-m-d', strtotime("-{$age} years"));
        return self::create(['dob' => $dob]);
    }
    
    /**
     * Generate pasien dengan gender tertentu
     * 
     * @param string $gender M atau F
     * @param int $count Jumlah data
     * @return array Array of patients
     */
    public static function createByGender(string $gender, int $count = 1): array
    {
        $patients = [];
        for ($i = 0; $i < $count; $i++) {
            $patients[] = self::create(['gender' => $gender]);
        }
        return $patients;
    }
}
