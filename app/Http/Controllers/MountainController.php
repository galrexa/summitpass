<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MountainController extends Controller
{
    /**
     * API: Dapatkan rekomendasi gunung untuk user berdasarkan pengalaman.
     * 
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommendations(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('limit', 5);
        
        $recommendations = $user->getRecommendedMountains($limit);
        
        return response()->json([
            'user_experience' => $user->highestSummitMdpl(),
            'recommendations' => $recommendations->map(fn($mountain) => [
                'id' => $mountain->id,
                'name' => $mountain->name,
                'height_mdpl' => $mountain->height_mdpl,
                'grade' => $mountain->grade,
                'location' => $mountain->location,
                'province' => $mountain->province,
                'image_url' => $mountain->image_url,
                'is_eligible' => $user->isEligibleForMountain($mountain),
                'required_experience' => $user->getRequiredExperienceFor($mountain),
            ])
        ]);
    }
    
    /**
     * API: Cek eligibility user untuk gunung tertentu.
     * 
     * @param  int  $mountainId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEligibility($mountainId)
    {
        $user = Auth::user();
        $mountain = Mountain::with('regulation')->findOrFail($mountainId);
        
        $isEligible = $user->isEligibleForMountain($mountain);
        $userExp = $user->highestSummitMdpl();
        $requiredExp = $user->getRequiredExperienceFor($mountain);
        
        return response()->json([
            'is_eligible' => $isEligible,
            'user_experience' => $userExp,
            'required_experience' => $requiredExp,
            'gap' => $requiredExp ? max(0, $requiredExp - $userExp) : 0,
            'message' => $isEligible 
                ? 'Anda memenuhi syarat untuk mendaki gunung ini.'
                : sprintf(
                    'Anda memerlukan pengalaman %d MDPL (saat ini: %d MDPL)',
                    $requiredExp,
                    $userExp
                )
        ]);
    }
}

// Made with Bob
