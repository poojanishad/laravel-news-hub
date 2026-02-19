<?php

namespace App\Services;

use App\Models\UserPreference;

class PreferenceService
{
    public function getUserPreference($user)
    {
        if (!$user) {
            return null;
        }

        return UserPreference::where('user_id', $user->id)->first();
    }

    public function save($user, array $data)
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'sources' => $data['sources'] ?? [],
                'categories' => $data['categories'] ?? [],
                'authors' => $data['authors'] ?? [],
            ]
        );
    }

    public function clear($user)
    {
        return UserPreference::where('user_id', $user->id)->delete();
    }
}
