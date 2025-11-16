<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ProductsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Exemples de produits
        return [
            [
                'Smartphone Samsung Galaxy A54',
                '1299.00',
                'Smartphone 5G avec écran AMOLED 6.4", 128GB de stockage, appareil photo 50MP. Couleur: Noir.',
                'Électronique',
                '25',
                'SAM-A54-128-BLK'
            ],
            [
                'T-Shirt Homme Coton',
                '45.00',
                'T-shirt en coton 100%, col rond, disponible en plusieurs tailles. Coupe regular.',
                'Mode Homme',
                '100',
                'TSHIRT-M-001'
            ],
            [
                'Canapé 3 Places Moderne',
                '2499.00',
                'Canapé confortable avec revêtement en tissu gris, pieds en bois. Dimensions: 210x90x85cm.',
                'Maison & Décor',
                '5',
                'CANAPE-3P-GREY'
            ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'nom',
            'prix',
            'description',
            'categorie',
            'stock',
            'sku',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour l'en-tête
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 35,  // nom
            'B' => 12,  // prix
            'C' => 60,  // description
            'D' => 20,  // categorie
            'E' => 10,  // stock
            'F' => 20,  // sku
        ];
    }
}
