<?php

namespace App\Http\Requests;

use App\Game;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\Yaml\Yaml;

class CreateRandomizedGame extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $startingData = Yaml::parse(file_get_contents(base_path('vendor/oracles/randomizer/romdata/startingData.yaml')));
        $startingPacks = Yaml::parse(file_get_contents(base_path('vendor/oracles/randomizer/romdata/startingPacks.yaml')));
        $rings = Yaml::parse(file_get_contents(base_path('vendor/oracles/randomizer/romdata/rings.yaml')));
        $valid_items = array_merge(
            array_keys($startingData['common']),
            array_keys($startingData[Request::get('game')]),
            array_keys($startingPacks[Request::get('game')]),
            $rings,
        );

        return [
            'game' => Rule::enum(Game::class),
            'hard' => 'nullable|boolean',
            'linked_items' => 'nullable|boolean',
            'cross_items' => 'nullable|boolean',
            'keysanity' => 'nullable|boolean',
            'auto_mermaid' => 'nullable|boolean',
            'dungeon_shuffle' => 'nullable|boolean',
            'fools_ore' => 'nullable|int',
            'essences' => 'nullable|int',
            'starting_items' => 'nullable|array',
            'starting_items.*' => Rule::in($valid_items),
        ];
    }
}
