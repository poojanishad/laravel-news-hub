<?php

namespace App\Services;

use App\Models\UserPreference;

class PreferenceService
{
    public function get($user)
    {
        return UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'sources' => [],
                'categories' => [],
                'authors' => [],
            ]
        );
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