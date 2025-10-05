<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PreEmploymentExamination;
use App\Models\User;

class LinkExaminationsToPatients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'examinations:link-patients {--dry-run : Show what would be linked without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link existing pre-employment examinations to patient accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }
        
        $this->info('Starting to link pre-employment examinations to patients...');
        
        // Get all examinations without patient_id
        $examinations = PreEmploymentExamination::whereNull('patient_id')
            ->with(['preEmploymentRecord'])
            ->get();
        
        $this->info("Found {$examinations->count()} examinations without patient links");
        
        $linked = 0;
        $notFound = 0;
        
        foreach ($examinations as $examination) {
            $patient = $this->findPatientForExamination($examination);
            
            if ($patient) {
                $this->line("✓ Linking examination #{$examination->id} ({$examination->name}) to patient: {$patient->fname} {$patient->lname} ({$patient->email})");
                
                if (!$dryRun) {
                    $examination->update(['patient_id' => $patient->id]);
                }
                $linked++;
            } else {
                $this->warn("✗ No patient found for examination #{$examination->id} ({$examination->name})");
                $notFound++;
            }
        }
        
        $this->newLine();
        $this->info("Summary:");
        $this->info("- Linked: {$linked}");
        $this->info("- Not found: {$notFound}");
        
        if ($dryRun) {
            $this->info("Run without --dry-run to apply changes");
        } else {
            $this->info("Linking completed!");
        }
    }
    
    /**
     * Find a patient account for the given examination
     */
    private function findPatientForExamination($examination)
    {
        $patients = User::where('role', 'patient')->get();
        
        // Try multiple matching strategies
        foreach ($patients as $patient) {
            $patientFullName = trim($patient->fname . ' ' . $patient->lname);
            
            // Strategy 1: Exact email match with pre-employment record
            if ($examination->preEmploymentRecord && 
                $examination->preEmploymentRecord->email === $patient->email) {
                return $patient;
            }
            
            // Strategy 2: Exact name match with examination name
            if ($examination->name && 
                strtolower($examination->name) === strtolower($patientFullName)) {
                return $patient;
            }
            
            // Strategy 3: Name match with pre-employment record
            if ($examination->preEmploymentRecord) {
                $recordFullName = trim($examination->preEmploymentRecord->first_name . ' ' . $examination->preEmploymentRecord->last_name);
                if (strtolower($recordFullName) === strtolower($patientFullName)) {
                    return $patient;
                }
            }
            
            // Strategy 4: Partial name match (first and last name)
            if ($examination->preEmploymentRecord &&
                strtolower($examination->preEmploymentRecord->first_name) === strtolower($patient->fname) &&
                strtolower($examination->preEmploymentRecord->last_name) === strtolower($patient->lname)) {
                return $patient;
            }
        }
        
        return null;
    }
}
