<?php

namespace App\Services\Admin;

use App\Models\Content;
use App\Models\Extra;
use Illuminate\Support\Collection;

class ContentExtraService
{
    /**
     * @param Content $content
     * @return Extra[]|Collection|array
     */
    public function index(Content $content): Collection
    {
        return $content->extras;
    }

    /**
     * @param Content $content
     * @param array $requestData
     * @return Extra
     */
    public function store(Content $content, array $requestData = []): Extra
    {
        return $content->extras()->create($requestData);
    }

    /**
     * @param Content $content
     * @param Extra $extra
     * @param array $requestData
     * @return Extra
     */
    public function update(Content $content, Extra $extra, array $requestData = []): Extra
    {
        $content->extras()->find($extra->id)->update($requestData);

        return $extra;
    }

    /**
     * @param Content $content
     * @param Extra $extra
     * @return bool|null
     */
    public function delete(Content $content, Extra $extra): bool|null
    {
        return $content->extras()->find($extra->id)->delete();
    }
}
