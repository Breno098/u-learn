<?php

namespace App\Services\Admin;

use App\Models\Content;
use Illuminate\Database\Eloquent\Collection;

class TopContentService
{
     /**
     * @param integer $topQuantity
     * @return \Illuminate\Support\Collection
     */
    public function getTopPosition(int $topQuantity = 10): \Illuminate\Support\Collection
    {
        $contentsWithDefinedPosition = $this->getContentsWithDefinedPosition($topQuantity);

        $contentsWithoutDefinedPosition = $this->getContentsWithoutDefinedPosition(
            $topQuantity, $contentsWithDefinedPosition->count()
        );

        return collect(
            $this->generateTopPositions($contentsWithDefinedPosition, $contentsWithoutDefinedPosition)
        )->sortKeys();
    }

    /**
     * @param Collection $contentsWithDefinedPosition
     * @param Collection $contentsWithoutDefinedPosition
     * @return array
     */
    private function generateTopPositions(Collection $contentsWithDefinedPosition, Collection $contentsWithoutDefinedPosition): array
    {
        $contents = [];

        $lastKey = 1;
        foreach ($contentsWithDefinedPosition as $content) {
            $lastKey = $content->top_position;

            $next = true;
            while ($next) {
                if (isset($contents[$lastKey])) {
                    $lastKey++;
                } else {
                    $contents[$lastKey] = $content;
                    $next = false;
                }
            }
        }

        $lastKey = 1;
        foreach ($contentsWithoutDefinedPosition as $content) {
            $next = true;
            while ($next) {
                if (isset($contents[$lastKey])) {
                    $lastKey++;
                } else {
                    $contents[$lastKey] = $content;
                    $next = false;
                }
            }
        }

        return $contents;
    }

    /**
     * @param integer $topQuantity
     * @return Collection
     */
    private function getContentsWithDefinedPosition(int $topQuantity): \Illuminate\Database\Eloquent\Collection
    {
        return Content::orderBy('top_position', 'asc')
            ->orderBy('access_count', 'desc')
            ->whereNotNull('top_position')
            ->take($topQuantity)
            ->get();
    }

     /**
     * @param integer $topQuantity
     * @return Collection
     */
    private function getContentsWithoutDefinedPosition(int $topQuantity, int $definedPositions): Collection
    {
        return Content::orderBy('access_count', 'desc')
            ->whereNull('top_position')
            ->when(
                $definedPositions <= $topQuantity,
                function($query) use ($topQuantity, $definedPositions){
                    $query->take($topQuantity - $definedPositions);
                }
            )->get();
    }

    /**
     * @param Content $content
     * @param integer $position
     * @return boolean
     */
    public function setPosition(Content $content, int $position): bool
    {
        $nextFreePosition = $this->nextFreePosition($content, $position);

        return $content->update([
            'top_position' => $nextFreePosition
        ]);
    }

    /**
     * @param Content $content
     * @param integer $position
     * @return integer
     */
    private function nextFreePosition(Content $content, int $position): int
    {
        if ($content->top_position === $position) {
           return $position;
        }

        $next = true;
        while ($next) {
            $contentGet = Content::where('top_position', $position)->first();

            if ($contentGet) {
                $position++;
            } else {
                $next = false;
            }
        }

        return $position;
    }

    /**
     * @param Content $content
     * @return boolean
     */
    public function removeTopPosition(Content $content): bool
    {
        return $content->update(['top_position' => null]);
    }
}
