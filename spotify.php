<?php

$client_id = '';
$client_secret = '';


if(isset($_POST['link']) && !empty($_POST['link'])) {
    $spotify_song_link = $_POST['link'];
} else {
    $spotify_song_link = 'https://open.spotify.com/track/0XmK2zPgY1t1msdf1jyhHw';
}

$song_id = substr($spotify_song_link, strrpos($spotify_song_link, '/') + 1);

// Set the API endpoint URL
$url = "https://accounts.spotify.com/api/token";

// Set the parameters for the API request
$params = array(
    'grant_type' => 'client_credentials'
);

// Set the headers for the API request
$headers = array(
    'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),
    'Content-Type: application/x-www-form-urlencoded'
);

// Send the API request and get the response
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Decode the response JSON and get the access token

$data = json_decode($response, true);

// print_r($access_token);


if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $access_token = $data['access_token'];
    //$access_token = 'BQDdIgfh81f_V3NlXiUKeQtt6MzdeXrqk4Y4kbk_LQdfBRnqK8iOwtw9_HwcYMkhwqi5T0qhAvedxiG3lp9lYlGf--VVztEeoDQZM-Ykt6FheMSAnAb-';
        if(isset($_POST['download']) && !empty($_POST['link'])) {
            // Set the API endpoint URL to get the song details
        $url = "https://api.spotify.com/v1/tracks/$song_id";

        // Set the headers for the API request
        $headers = array(
            'Authorization: Bearer ' . $access_token
        );

        // Send the API request and get the response
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode the response JSON and get the preview URL for the song
        $data = json_decode($response, true);

        if(isset($data['preview_url']) && !empty($data['preview_url'])) {
            $preview_url = $data['preview_url'];
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Song not found!'
            ]);
            exit;
        }

        $download = true;
        // Download the song
        if($download) {
            if(file_put_contents('song.mp3', file_get_contents($preview_url))) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Song downloaded successfully!',
                    'link' => 'song.mp3'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error downloading the song!'
                ]);
            };
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Download is disabled!',
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method!'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method!'
    ]);
}
?>
