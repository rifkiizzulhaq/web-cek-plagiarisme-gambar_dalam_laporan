<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClearDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all uploaded documents and plagiarism data while keeping user accounts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Clearing all documents and plagiarism data...');

        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Clear Laravel database tables (order matters due to foreign keys)
            DB::table('image_plagiarism_reports')->truncate();
            DB::table('files')->truncate();
            $this->info('✓ Laravel tables cleared');

            // Clear Python database tables
            DB::statement('TRUNCATE TABLE hash_locations');
            DB::statement('TRUNCATE TABLE sentence_hashes');
            DB::statement('TRUNCATE TABLE image_embeddings');
            DB::statement('TRUNCATE TABLE documents');
            DB::statement('TRUNCATE TABLE document_hashes');
            $this->info('✓ Python database tables cleared');
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Clear uploaded files from storage
            $files = Storage::disk('public')->allFiles('uploads');
            Storage::disk('public')->delete($files);
            $this->info('✓ Uploaded files cleared');

            // Clear Python extracted images
            $pythonImagesPath = base_path('Python/extract_data_py/images');
            if (is_dir($pythonImagesPath)) {
                $files = glob($pythonImagesPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                $this->info('✓ Python extracted images cleared');
            }

            $this->info('🎉 All documents and plagiarism data cleared successfully!');
            $this->info('👤 User accounts remain intact');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error clearing documents: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
