<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SpotifyController extends BaseController
{
    public function redirectToSpotify()
    {
        return Socialite::driver('spotify')->scopes(['user-read-email'])->redirect();
    }

    public function handleSpotifyCallback(Request $request)
    {
        try {
            $spotifyUser = Socialite::driver('spotify')->user();
            Log::info('Spotify User Info:', ['spotifyUser' => $spotifyUser]);

            $user = Auth::user();

            if ($user) {
                $user->spotify_id = $spotifyUser->id;
                $user->spotify_token = $spotifyUser->token;
                $user->spotify_refresh_token = $spotifyUser->refreshToken;
                $user->spotify_expires_in = $spotifyUser->expiresIn;
                $user->save();

                return response()->json([
                    'message' => 'Spotify linked successfully.',
                    'spotify_user' => $spotifyUser,
                ], 200);
            } else {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
        } catch (\Exception $e) {
            Log::error('Error in Spotify callback', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Authentication failed'], 500);
        }
    }

    
public function getSpotifyPlaylists(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    $token = $user->spotify_token;

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->get('https://api.spotify.com/v1/me/playlists');

    if ($response->successful()) {
        $playlists = $response->json()['items'];
        return response()->json(['playlists' => $playlists], 200);
    } else {
        return response()->json(['error' => 'Failed to fetch playlists'], $response->status());
    }
}
}
