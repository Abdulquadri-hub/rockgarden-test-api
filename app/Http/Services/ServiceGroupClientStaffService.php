<?php

namespace App\Http\Services;

use App\Models\ServiceGroup;
use App\Models\ServiceGroupClient;
use App\Models\ServiceGroupStaff;
use Illuminate\Support\Facades\Log;

class ServiceGroupClientStaffService
{
    public function addStaffToGroup($groupId, $staff_ids){
        try {
            $res = [];
            foreach ($staff_ids as $staff_id){
                $res[] = ServiceGroupStaff::firstOrCreate(
                    [
                        'group_id' => $groupId,
                        'staff_id' => $staff_id
                    ],
                );
            }
            return [
                'success' => true,
                'message' => $res
            ];
        }catch (\Exception $e){
            Log::debug($e);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function addClientToGroup($groupId, $client_ids){
        try {
            $res = [];
            foreach ($client_ids as $client_id){
                $res[] = ServiceGroupClient::firstOrCreate(
                    [
                        'group_id' => $groupId,
                        'client_id' => $client_id
                    ],
                );
            }
            return [
                'success' => true,
                'message' => $res
            ];
        }catch (\Exception $e){
            Log::debug($e);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateStaffToGroup($groupId, $staff_ids){
        try {
            $res = [];
            ServiceGroupStaff::where('group_id', $groupId)->delete();
            foreach ($staff_ids as $staff_id){
                $res[] = ServiceGroupStaff::firstOrCreate(
                    [
                        'group_id' => $groupId,
                        'staff_id' => $staff_id
                    ],
                );
            }
            return [
                'success' => true,
                'message' => $res
            ];
        }catch (\Exception $e){
            Log::debug($e);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateClientToGroup($groupId, $client_ids){
        try {
            $res = [];
            ServiceGroupClient::where('group_id', $groupId)->delete();
            foreach ($client_ids as $client_id){
                $res[] = ServiceGroupClient::firstOrCreate(
                    [
                        'group_id' => $groupId,
                        'client_id' => $client_id
                    ],
                );
            }
            return [
                'success' => true,
                'message' => $res
            ];
        }catch (\Exception $e){
            Log::debug($e);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


}
