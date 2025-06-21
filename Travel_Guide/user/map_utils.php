<?php
/**
 * Open-source mapping utilities for IITG Travel Guide
 * Uses OSRM (Open Source Routing Machine) and OpenStreetMap
 */

// IIT Guwahati coordinates
define('IITG_LAT', 26.1923);
define('IITG_LNG', 91.6951);

/**
 * Calculate distance using OSRM API
 */
function calculateDistanceOSRM($lat1, $lng1, $lat2, $lng2) {
    $osrm_url = "http://router.project-osrm.org/route/v1/driving/";
    $url = $osrm_url . "$lng1,$lat1;$lng2,$lat2?overview=false";
    
    $options = [
        'http' => [
            'method' => 'GET',
            'timeout' => 3 // 3 second timeout
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        return haversineDistance($lat1, $lng1, $lat2, $lng2);
    }
    
    $data = json_decode($response, true);
    return isset($data['routes'][0]['distance']) ? 
        round($data['routes'][0]['distance'] / 1000, 1) : // Convert to km
        haversineDistance($lat1, $lng1, $lat2, $lng2);
}

/**
 * Haversine formula fallback
 */
function haversineDistance($lat1, $lng1, $lat2, $lng2) {
    $earth_radius = 6371; // km
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    
    $a = sin($dLat/2) * sin($dLat/2) + 
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
         sin($dLng/2) * sin($dLng/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return round($earth_radius * $c, 1);
}

/**
 * Calculate route distance for multiple points
 */
function calculateRouteDistance($points) {
    if (count($points) < 2) return 0;
    
    $waypoints = implode(';', array_map(function($point) {
        return "{$point['lng']},{$point['lat']}";
    }, $points));
    
    $osrm_url = "http://router.project-osrm.org/route/v1/driving/";
    $url = $osrm_url . $waypoints . "?overview=false";
    
    $options = [
        'http' => [
            'method' => 'GET',
            'timeout' => 5
        ]
    ];
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        // Calculate sum of haversine distances
        $total = 0;
        for ($i = 0; $i < count($points) - 1; $i++) {
            $total += haversineDistance(
                $points[$i]['lat'],
                $points[$i]['lng'],
                $points[$i+1]['lat'],
                $points[$i+1]['lng']
            );
        }
        return $total;
    }
    
    $data = json_decode($response, true);
    return isset($data['routes'][0]['distance']) ? 
        round($data['routes'][0]['distance'] / 1000, 1) : 0;
}

/**
 * Estimate transport cost
 */
function estimateTransportCost($distance_km, $mode) {
    $rates = [
        'Bus' => 8,    // ₹8 per km
        'Train' => 5,   // ₹5 per km
        'Car' => 10,    // ₹10 per km (petrol)
        'Bike' => 4,    // ₹4 per km
        'Flight' => 15  // ₹15 per km (approximate)
    ];
    
    $rate = $rates[$mode] ?? 8; // Default to bus rate
    return round($distance_km * $rate * 2); // Round trip
}

/**
 * Get static map image from OpenStreetMap
 */
function getStaticMap($points, $width = 600, $height = 400) {
    if (empty($points)) return '';
    
    $markers = array_map(function($point, $i) {
        $color = $i === 0 ? 'green' : ($i === count($points)-1 ? 'red' : 'blue');
        return "color:$color|label:" . ($i+1) . "|{$point['lat']},{$point['lng']}";
    }, $points, array_keys($points));
    
    $markers_str = implode('&markers=', $markers);
    
    return "https://maps.googleapis.com/maps/api/staticmap?" .
           "size={$width}x{$height}" .
           "&markers=$markers_str" .
           "&path=color:0x0000ff|weight:5|" . 
           implode('|', array_map(function($p) { return "{$p['lat']},{$p['lng']}"; }, $points)) .
           "&key=YOUR_OPENSTREETMAP_KEY"; // Note: OSM doesn't require keys for static maps
}
?>