<?php

namespace App\Lib;

class MedicationDosageHelper {
    
    public static function normalizePaymentName($paymentName) {
        return trim(str_replace('Payment for', '', $paymentName));
    }
    
    public static function calculateDailyDosage($staffChart) {
        $totalDailyDosage = 0;
        
        // Check morning dose
        if ($staffChart->is_morning_dose_administered) {
            $totalDailyDosage += 1; // Increment by one dose
        }
        
        // Check afternoon dose
        if ($staffChart->is_afternoon_dose_administered) {
            $totalDailyDosage += 1;
        }
        
        // Check evening dose
        if ($staffChart->is_evening_dose_administered) {
            $totalDailyDosage += 1;
        }
        
        return $totalDailyDosage;
    }

    public static function getDosageDescription($staffChart) {
        $parts = [];
        
        if ($staffChart->is_morning_dose_administered) {
            $parts[] = "1 dose morning" . 
                      (!empty($staffChart->dosage_morning_when) ? " ({$staffChart->dosage_morning_when})" : "");
        }
        
        if ($staffChart->is_afternoon_dose_administered) {
            $parts[] = "1 dose afternoon" . 
                      (!empty($staffChart->dosage_afternoon_when) ? " ({$staffChart->dosage_afternoon_when})" : "");
        }
        
        if ($staffChart->is_evening_dose_administered) {
            $parts[] = "1 dose evening" . 
                      (!empty($staffChart->dosage_evening_when) ? " ({$staffChart->dosage_evening_when})" : "");
        }
        
        if (empty($parts)) {
            return "As needed";
        }
        
        return implode(", ", $parts);
    }
}
