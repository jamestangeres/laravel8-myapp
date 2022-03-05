<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GithubController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $response = Http::withHeaders([
           'Accept' => 'application/vnd.github.v3+json'
        ])
            ->get("https://api.github.com/users/paulocastellano/repos");

        if ($response->failed())
        {
            return response()->json([],404);
        }

        $data = $response->collect();
        return response()->json($data, 202);
    }
}
