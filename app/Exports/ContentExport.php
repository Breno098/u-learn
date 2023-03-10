<?php

namespace App\Exports;

use App\Models\Content;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class ContentExport implements FromCollection, Responsable, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @var string
     */
    private string $fileName;

    /**
     * @var string
     */
    private string $writerType = Excel::XLSX;

    /**
     * @var Collection
     */
    private Collection $contents;

    /**
     * @param Collection|Content[] $contents
     */
    public function __construct(Collection $contents)
    {
        $this->fileName = "conteudo_". date('dmYHis'). ".xlsx";
        $this->contents = $contents;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->contents;
    }

    /**
     * @param Content $content
     * @var array
     */
    public function map($content): array
    {
        return [
            'name' => $content->name,
            'category' => $content->category->name,
            'author' => $content->author,
            'sections' => implode(",", $content->sections->map->name->toArray()),
            'launch_start_at' => $content->launch_start_at ? $content->launch_start_at->format('d/m/Y H:i') : null,
            'launch_end_at' => $content->launch_end_at ? $content->launch_end_at->format('d/m/Y H:i') : null,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            "Nome",
            "Categoria",
            "Author",
            "Seções",
            "Lançamento",
            "Encerramento",
        ];
    }
}
