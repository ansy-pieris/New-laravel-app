<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixStorageSymlink extends Command
{
    protected $signature = 'storage:fix-symlink';
    protected $description = 'Fix storage symlink for Windows compatibility';

    public function handle()
    {
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');
        
        $this->info('Checking storage symlink...');
        
        // Check if symlink already exists and is working
        if (is_link($linkPath) && is_dir($linkPath)) {
            $this->info('✅ Storage symlink is working correctly.');
            return 0;
        }
        
        $this->warn('⚠️ Storage symlink is missing or broken. Fixing...');
        
        // Remove broken symlink if exists
        if (file_exists($linkPath) || is_link($linkPath)) {
            if (PHP_OS_FAMILY === 'Windows') {
                $this->info('Removing existing broken symlink (Windows)...');
                exec("rmdir /s /q \"$linkPath\" 2>nul");
            } else {
                $this->info('Removing existing broken symlink (Unix)...');
                unlink($linkPath);
            }
        }
        
        // Create new symlink
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                // Use Windows mklink command with relative path
                $relativePath = '..\\storage\\app\\public';
                $command = "mklink /D \"$linkPath\" \"$relativePath\"";
                $this->info("Executing: $command");
                
                $output = [];
                $return_var = 0;
                exec($command, $output, $return_var);
                
                if ($return_var === 0) {
                    $this->info('✅ Windows symlink created successfully!');
                } else {
                    $this->error('❌ Failed to create Windows symlink. Output: ' . implode("\n", $output));
                    return 1;
                }
            } else {
                symlink($targetPath, $linkPath);
                $this->info('✅ Unix symlink created successfully!');
            }
            
            // Verify the symlink works
            if (is_dir($linkPath)) {
                $this->info('✅ Symlink verification passed - storage directory is accessible.');
                
                // Check if products directory exists
                $productsPath = $linkPath . DIRECTORY_SEPARATOR . 'products';
                if (is_dir($productsPath)) {
                    $imageCount = count(glob($productsPath . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,png,gif}', GLOB_BRACE));
                    $this->info("✅ Products directory found with $imageCount images.");
                } else {
                    $this->warn('⚠️ Products directory not found. This is normal if no products have been uploaded yet.');
                }
            } else {
                $this->error('❌ Symlink created but verification failed.');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to create storage symlink: ' . $e->getMessage());
            return 1;
        }
        
        $this->info('🎉 Storage symlink has been fixed! Product images should now display correctly.');
        return 0;
    }
}