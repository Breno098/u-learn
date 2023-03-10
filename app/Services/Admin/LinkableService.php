<?php

namespace App\Services\Admin;

use App\Models\Chapter;
use App\Models\Content;
use App\Models\Season;

class LinkableService
{
    /**
     * @param null|Content $content
     * @param string $type
     * @return array
     */
    public static function getSeasonsOrChapters(null|Content $content, string $type): array
    {
        if(! $content) {
            return [];
        }

        $seasonsOrChapters = collect();

        if ($content->has_seasons) {
            if ($type === 'season') {
                $content->seasons->each(fn(Season $season) => $seasonsOrChapters->push([
                    'id' => $season->id,
                    'name' => $season->name
                ]));
            } else if ($type === 'chapter') {
                $content->seasons->each(fn(Season $season) => $season->chapters->each(fn(Chapter $chapter) => $seasonsOrChapters->push([
                    'id' => $chapter->id,
                    'name' => "{$chapter->name} ({$season->number}ª temp.: {$season->name})",
                ])));
            }
        }

        return array_values($seasonsOrChapters->toArray());
    }
}
