<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\ReportSubmission;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentationDemoCleanupSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('O PresentationDemoCleanupSeeder não deve ser executado em produção.');
        }

        DB::transaction(function () {
            $emails = ['porteiro.demo@siga.local', 'porteiro.apoio@siga.local', 'fiscal.demo@siga.local'];
            $plates = ['DME-1001', 'DME-1002', 'DME-1003', 'OFI-2001', 'OFI-2002'];
            $docs = ['90000000001', '90000000002', '90000000003', '90000000004', '90000000005'];

            $userIds = User::whereIn('email', $emails)->pluck('id');
            $vehicleIds = Vehicle::withTrashed()->whereIn('license_plate', $plates)->pluck('id');
            $driverIds = Driver::withTrashed()->whereIn('document', $docs)->pluck('id');

            $submissionIds = ReportSubmission::query()
                ->whereIn('guard_id', $userIds)
                ->orWhereIn('vehicle_id', $vehicleIds)
                ->pluck('id');

            PrivateEntry::whereIn('report_submission_id', $submissionIds)->update(['report_submission_id' => null]);
            OfficialTrip::whereIn('report_submission_id', $submissionIds)->update(['report_submission_id' => null]);
            ReportSubmission::whereIn('id', $submissionIds)->delete();

            PrivateEntry::whereIn('vehicle_id', $vehicleIds)->delete();
            OfficialTrip::whereIn('vehicle_id', $vehicleIds)->delete();

            DB::table('driver_vehicle')
                ->whereIn('vehicle_id', $vehicleIds)
                ->orWhereIn('driver_id', $driverIds)
                ->delete();

            Vehicle::withTrashed()->whereIn('license_plate', $plates)->forceDelete();
            Driver::withTrashed()->whereIn('document', $docs)->forceDelete();
            User::whereIn('email', $emails)->delete();
        });

        $this->command?->info('Dados de apresentação removidos.');
    }
}
