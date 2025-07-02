<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Karyawan;

class FaceRecognitionHelper
{
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + 
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        
        return $meters;
    }

    public static function isWithinOfficeRadius($userLat, $userLng, $karyawan = null)
    {
        if (!$karyawan) {
            $karyawan = Auth::guard('karyawan')->user();
        }

        if (empty($karyawan->office_location)) {
            return [
                'within' => false,
                'distance' => 0,
                'max_distance' => 0,
                'error' => 'Lokasi kantor belum ditentukan'
            ];
        }

        $officeCoords = $karyawan->office_location['coordinates'];
        $officeLng = $officeCoords[0]; // Longitude
        $officeLat = $officeCoords[1]; // Latitude
        $maxDistance = $karyawan->office_radius ?? 55;
        
        $distance = self::calculateDistance($userLat, $userLng, $officeLat, $officeLng);
        
        Log::info('Distance Calculation:', [
            'user_lat' => $userLat,
            'user_lng' => $userLng,
            'office_lat' => $officeLat,
            'office_lng' => $officeLng,
            'distance' => $distance,
            'max_distance' => $maxDistance
        ]);
        
        return [
            'within' => $distance <= $maxDistance,
            'distance' => $distance,
            'max_distance' => $maxDistance,
            'error' => null
        ];
    }

    public static function logFaceRecognitionEvent($eventData)
    {
        try {
            $logPath = storage_path('logs/face_recognition.log');
            $logData = json_encode([
                'timestamp' => now()->toDateTimeString(),
                'event' => $eventData
            ]) . PHP_EOL;
            
            file_put_contents($logPath, $logData, FILE_APPEND);
        } catch (\Exception $e) {
            Log::error('Failed to log face recognition event: ' . $e->getMessage());
        }
    }
}