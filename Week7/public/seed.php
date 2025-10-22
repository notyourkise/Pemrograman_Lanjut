<?php
/**
 * Seeding Script - Generate Dummy Data
 * 
 * Script untuk generate data dummy menggunakan PatientFactory
 * Jalankan: php public/seed.php
 */

declare(strict_types=1);

// Load dependencies
require __DIR__ . '/../app/core/Database.php';
require __DIR__ . '/../app/core/Autoloader.php';
require __DIR__ . '/../app/repositories/PatientRepository.php';
require __DIR__ . '/../app/factories/PatientFactory.php';

// Load config
$config = require __DIR__ . '/../app/config.php';

echo "🌱 Starting seed...\n\n";

try {
    // Initialize repository
    $repo = new PatientRepository();
    
    // Generate 50 patients
    $count = 50;
    echo "📦 Generating {$count} patients...\n";
    $patients = PatientFactory::createMany($count);
    
    // Insert to database
    $success = 0;
    $failed = 0;
    
    foreach ($patients as $index => $patient) {
        try {
            $id = $repo->create($patient);
            $success++;
            
            // Progress indicator
            if (($index + 1) % 10 === 0) {
                echo "   ✓ Inserted " . ($index + 1) . " patients\n";
            }
        } catch (Exception $e) {
            $failed++;
            echo "   ✗ Failed to insert patient: " . $patient['name'] . " - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    echo "✅ Seeding completed!\n";
    echo "   • Success: {$success} patients\n";
    echo "   • Failed: {$failed} patients\n";
    echo "   • Total: {$count} patients\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
