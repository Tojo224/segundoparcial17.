<?php

namespace App\Modules\ReportesYDashboard\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportesExport implements FromArray, WithHeadings, WithStyles
{
    protected $datos;
    protected $encabezados;
    protected $titulo;

    public function __construct(array $datos, array $encabezados, string $titulo = '')
    {
        $this->datos = $datos;
        $this->encabezados = $encabezados;
        $this->titulo = $titulo;
    }

    public function array(): array
    {
        return $this->datos;
    }

    public function headings(): array
    {
        return $this->encabezados;
    }

    public function styles(Worksheet $sheet)
    {
        // Estilos para encabezados
        $sheet->getStyle('A1:' . $this->getLastColumn() . '1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
                'wrapText' => true,
            ],
        ]);

        // Auto-ancho de columnas
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        return [];
    }

    private function getLastColumn()
    {
        $count = count($this->encabezados);
        $letters = '';
        while ($count > 0) {
            $count--;
            $letters = chr(65 + ($count % 26)) . $letters;
            $count = (int)($count / 26);
        }
        return $letters;
    }
}
