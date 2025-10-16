<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipiosColombiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder de muestra de municipios de Colombia por departamento
     * Incluye las capitales y principales municipios
     */
    public function run(): void
    {
        $municipios = [
            // CUNDINAMARCA
            ['codigo_dane' => '11001', 'nombre' => 'Bogotá D.C.', 'departamento' => 'Bogotá D.C.', 'codigo_departamento' => '11', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '25001', 'nombre' => 'Agua de Dios', 'departamento' => 'Cundinamarca', 'codigo_departamento' => '25', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '25126', 'nombre' => 'Facatativá', 'departamento' => 'Cundinamarca', 'codigo_departamento' => '25', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '25175', 'nombre' => 'Fusagasugá', 'departamento' => 'Cundinamarca', 'codigo_departamento' => '25', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '25214', 'nombre' => 'Girardot', 'departamento' => 'Cundinamarca', 'codigo_departamento' => '25', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '25430', 'nombre' => 'Madrid', 'departamento' => 'Cundinamarca', 'codigo_departamento' => '25', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '25754', 'nombre' => 'Soacha', 'departamento' => 'Cundinamarca', 'codigo_departamento' => '25', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '25899', 'nombre' => 'Zipaquirá', 'departamento' => 'Cundinamarca', 'codigo_departamento' => '25', 'region' => 'Andina', 'capital_departamento' => false],

            // ANTIOQUIA
            ['codigo_dane' => '05001', 'nombre' => 'Medellín', 'departamento' => 'Antioquia', 'codigo_departamento' => '05', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '05088', 'nombre' => 'Bello', 'departamento' => 'Antioquia', 'codigo_departamento' => '05', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '05266', 'nombre' => 'Envigado', 'departamento' => 'Antioquia', 'codigo_departamento' => '05', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '05360', 'nombre' => 'Itagüí', 'departamento' => 'Antioquia', 'codigo_departamento' => '05', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '05615', 'nombre' => 'Rionegro', 'departamento' => 'Antioquia', 'codigo_departamento' => '05', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '05631', 'nombre' => 'Sabaneta', 'departamento' => 'Antioquia', 'codigo_departamento' => '05', 'region' => 'Andina', 'capital_departamento' => false],

            // VALLE DEL CAUCA
            ['codigo_dane' => '76001', 'nombre' => 'Cali', 'departamento' => 'Valle del Cauca', 'codigo_departamento' => '76', 'region' => 'Pacífica', 'capital_departamento' => true],
            ['codigo_dane' => '76111', 'nombre' => 'Buenaventura', 'departamento' => 'Valle del Cauca', 'codigo_departamento' => '76', 'region' => 'Pacífica', 'capital_departamento' => false],
            ['codigo_dane' => '76364', 'nombre' => 'Jamundí', 'departamento' => 'Valle del Cauca', 'codigo_departamento' => '76', 'region' => 'Pacífica', 'capital_departamento' => false],
            ['codigo_dane' => '76520', 'nombre' => 'Palmira', 'departamento' => 'Valle del Cauca', 'codigo_departamento' => '76', 'region' => 'Pacífica', 'capital_departamento' => false],
            ['codigo_dane' => '76892', 'nombre' => 'Yumbo', 'departamento' => 'Valle del Cauca', 'codigo_departamento' => '76', 'region' => 'Pacífica', 'capital_departamento' => false],

            // ATLÁNTICO
            ['codigo_dane' => '08001', 'nombre' => 'Barranquilla', 'departamento' => 'Atlántico', 'codigo_departamento' => '08', 'region' => 'Caribe', 'capital_departamento' => true],
            ['codigo_dane' => '08078', 'nombre' => 'Baranoa', 'departamento' => 'Atlántico', 'codigo_departamento' => '08', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '08296', 'nombre' => 'Galapa', 'departamento' => 'Atlántico', 'codigo_departamento' => '08', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '08433', 'nombre' => 'Malambo', 'departamento' => 'Atlántico', 'codigo_departamento' => '08', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '08758', 'nombre' => 'Soledad', 'departamento' => 'Atlántico', 'codigo_departamento' => '08', 'region' => 'Caribe', 'capital_departamento' => false],

            // SANTANDER
            ['codigo_dane' => '68001', 'nombre' => 'Bucaramanga', 'departamento' => 'Santander', 'codigo_departamento' => '68', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '68276', 'nombre' => 'Floridablanca', 'departamento' => 'Santander', 'codigo_departamento' => '68', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '68307', 'nombre' => 'Girón', 'departamento' => 'Santander', 'codigo_departamento' => '68', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '68547', 'nombre' => 'Piedecuesta', 'departamento' => 'Santander', 'codigo_departamento' => '68', 'region' => 'Andina', 'capital_departamento' => false],

            // BOLÍVAR
            ['codigo_dane' => '13001', 'nombre' => 'Cartagena', 'departamento' => 'Bolívar', 'codigo_departamento' => '13', 'region' => 'Caribe', 'capital_departamento' => true],
            ['codigo_dane' => '13006', 'nombre' => 'Achí', 'departamento' => 'Bolívar', 'codigo_departamento' => '13', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '13430', 'nombre' => 'Magangué', 'departamento' => 'Bolívar', 'codigo_departamento' => '13', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '13838', 'nombre' => 'Turbaco', 'departamento' => 'Bolívar', 'codigo_departamento' => '13', 'region' => 'Caribe', 'capital_departamento' => false],

            // NORTE DE SANTANDER
            ['codigo_dane' => '54001', 'nombre' => 'Cúcuta', 'departamento' => 'Norte de Santander', 'codigo_departamento' => '54', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '54498', 'nombre' => 'Ocaña', 'departamento' => 'Norte de Santander', 'codigo_departamento' => '54', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '54518', 'nombre' => 'Pamplona', 'departamento' => 'Norte de Santander', 'codigo_departamento' => '54', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '54874', 'nombre' => 'Villa del Rosario', 'departamento' => 'Norte de Santander', 'codigo_departamento' => '54', 'region' => 'Andina', 'capital_departamento' => false],

            // TOLIMA
            ['codigo_dane' => '73001', 'nombre' => 'Ibagué', 'departamento' => 'Tolima', 'codigo_departamento' => '73', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '73268', 'nombre' => 'Espinal', 'departamento' => 'Tolima', 'codigo_departamento' => '73', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '73461', 'nombre' => 'Melgar', 'departamento' => 'Tolima', 'codigo_departamento' => '73', 'region' => 'Andina', 'capital_departamento' => false],

            // CALDAS
            ['codigo_dane' => '17001', 'nombre' => 'Manizales', 'departamento' => 'Caldas', 'codigo_departamento' => '17', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '17088', 'nombre' => 'Belalcázar', 'departamento' => 'Caldas', 'codigo_departamento' => '17', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '17174', 'nombre' => 'Chinchiná', 'departamento' => 'Caldas', 'codigo_departamento' => '17', 'region' => 'Andina', 'capital_departamento' => false],

            // RISARALDA
            ['codigo_dane' => '66001', 'nombre' => 'Pereira', 'departamento' => 'Risaralda', 'codigo_departamento' => '66', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '66170', 'nombre' => 'Dosquebradas', 'departamento' => 'Risaralda', 'codigo_departamento' => '66', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '66687', 'nombre' => 'Santa Rosa de Cabal', 'departamento' => 'Risaralda', 'codigo_departamento' => '66', 'region' => 'Andina', 'capital_departamento' => false],

            // QUINDÍO
            ['codigo_dane' => '63001', 'nombre' => 'Armenia', 'departamento' => 'Quindío', 'codigo_departamento' => '63', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '63111', 'nombre' => 'Calarcá', 'departamento' => 'Quindío', 'codigo_departamento' => '63', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '63190', 'nombre' => 'Circasia', 'departamento' => 'Quindío', 'codigo_departamento' => '63', 'region' => 'Andina', 'capital_departamento' => false],

            // BOYACÁ
            ['codigo_dane' => '15001', 'nombre' => 'Tunja', 'departamento' => 'Boyacá', 'codigo_departamento' => '15', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '15176', 'nombre' => 'Chiquinquirá', 'departamento' => 'Boyacá', 'codigo_departamento' => '15', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '15238', 'nombre' => 'Duitama', 'departamento' => 'Boyacá', 'codigo_departamento' => '15', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '15759', 'nombre' => 'Sogamoso', 'departamento' => 'Boyacá', 'codigo_departamento' => '15', 'region' => 'Andina', 'capital_departamento' => false],

            // META
            ['codigo_dane' => '50001', 'nombre' => 'Villavicencio', 'departamento' => 'Meta', 'codigo_departamento' => '50', 'region' => 'Orinoquía', 'capital_departamento' => true],
            ['codigo_dane' => '50006', 'nombre' => 'Acacías', 'departamento' => 'Meta', 'codigo_departamento' => '50', 'region' => 'Orinoquía', 'capital_departamento' => false],
            ['codigo_dane' => '50313', 'nombre' => 'Granada', 'departamento' => 'Meta', 'codigo_departamento' => '50', 'region' => 'Orinoquía', 'capital_departamento' => false],

            // HUILA
            ['codigo_dane' => '41001', 'nombre' => 'Neiva', 'departamento' => 'Huila', 'codigo_departamento' => '41', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '41244', 'nombre' => 'Garzón', 'departamento' => 'Huila', 'codigo_departamento' => '41', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '41551', 'nombre' => 'Pitalito', 'departamento' => 'Huila', 'codigo_departamento' => '41', 'region' => 'Andina', 'capital_departamento' => false],

            // NARIÑO
            ['codigo_dane' => '52001', 'nombre' => 'Pasto', 'departamento' => 'Nariño', 'codigo_departamento' => '52', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '52356', 'nombre' => 'Ipiales', 'departamento' => 'Nariño', 'codigo_departamento' => '52', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '52835', 'nombre' => 'Tumaco', 'departamento' => 'Nariño', 'codigo_departamento' => '52', 'region' => 'Pacífica', 'capital_departamento' => false],

            // CAUCA
            ['codigo_dane' => '19001', 'nombre' => 'Popayán', 'departamento' => 'Cauca', 'codigo_departamento' => '19', 'region' => 'Andina', 'capital_departamento' => true],
            ['codigo_dane' => '19517', 'nombre' => 'Patía', 'departamento' => 'Cauca', 'codigo_departamento' => '19', 'region' => 'Andina', 'capital_departamento' => false],
            ['codigo_dane' => '19698', 'nombre' => 'Santander de Quilichao', 'departamento' => 'Cauca', 'codigo_departamento' => '19', 'region' => 'Andina', 'capital_departamento' => false],

            // CÓRDOBA
            ['codigo_dane' => '23001', 'nombre' => 'Montería', 'departamento' => 'Córdoba', 'codigo_departamento' => '23', 'region' => 'Caribe', 'capital_departamento' => true],
            ['codigo_dane' => '23162', 'nombre' => 'Cereté', 'departamento' => 'Córdoba', 'codigo_departamento' => '23', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '23417', 'nombre' => 'Lorica', 'departamento' => 'Córdoba', 'codigo_departamento' => '23', 'region' => 'Caribe', 'capital_departamento' => false],

            // CESAR
            ['codigo_dane' => '20001', 'nombre' => 'Valledupar', 'departamento' => 'Cesar', 'codigo_departamento' => '20', 'region' => 'Caribe', 'capital_departamento' => true],
            ['codigo_dane' => '20011', 'nombre' => 'Aguachica', 'departamento' => 'Cesar', 'codigo_departamento' => '20', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '20621', 'nombre' => 'Río de Oro', 'departamento' => 'Cesar', 'codigo_departamento' => '20', 'region' => 'Caribe', 'capital_departamento' => false],

            // MAGDALENA
            ['codigo_dane' => '47001', 'nombre' => 'Santa Marta', 'departamento' => 'Magdalena', 'codigo_departamento' => '47', 'region' => 'Caribe', 'capital_departamento' => true],
            ['codigo_dane' => '47189', 'nombre' => 'Ciénaga', 'departamento' => 'Magdalena', 'codigo_departamento' => '47', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '47551', 'nombre' => 'Plato', 'departamento' => 'Magdalena', 'codigo_departamento' => '47', 'region' => 'Caribe', 'capital_departamento' => false],

            // LA GUAJIRA
            ['codigo_dane' => '44001', 'nombre' => 'Riohacha', 'departamento' => 'La Guajira', 'codigo_departamento' => '44', 'region' => 'Caribe', 'capital_departamento' => true],
            ['codigo_dane' => '44430', 'nombre' => 'Maicao', 'departamento' => 'La Guajira', 'codigo_departamento' => '44', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '44847', 'nombre' => 'Uribia', 'departamento' => 'La Guajira', 'codigo_departamento' => '44', 'region' => 'Caribe', 'capital_departamento' => false],

            // SUCRE
            ['codigo_dane' => '70001', 'nombre' => 'Sincelejo', 'departamento' => 'Sucre', 'codigo_departamento' => '70', 'region' => 'Caribe', 'capital_departamento' => true],
            ['codigo_dane' => '70204', 'nombre' => 'Corozal', 'departamento' => 'Sucre', 'codigo_departamento' => '70', 'region' => 'Caribe', 'capital_departamento' => false],
            ['codigo_dane' => '70670', 'nombre' => 'San Marcos', 'departamento' => 'Sucre', 'codigo_departamento' => '70', 'region' => 'Caribe', 'capital_departamento' => false],

            // CASANARE
            ['codigo_dane' => '85001', 'nombre' => 'Yopal', 'departamento' => 'Casanare', 'codigo_departamento' => '85', 'region' => 'Orinoquía', 'capital_departamento' => true],
            ['codigo_dane' => '85010', 'nombre' => 'Aguazul', 'departamento' => 'Casanare', 'codigo_departamento' => '85', 'region' => 'Orinoquía', 'capital_departamento' => false],
            ['codigo_dane' => '85400', 'nombre' => 'Villanueva', 'departamento' => 'Casanare', 'codigo_departamento' => '85', 'region' => 'Orinoquía', 'capital_departamento' => false],

            // ARAUCA
            ['codigo_dane' => '81001', 'nombre' => 'Arauca', 'departamento' => 'Arauca', 'codigo_departamento' => '81', 'region' => 'Orinoquía', 'capital_departamento' => true],
            ['codigo_dane' => '81065', 'nombre' => 'Arauquita', 'departamento' => 'Arauca', 'codigo_departamento' => '81', 'region' => 'Orinoquía', 'capital_departamento' => false],
            ['codigo_dane' => '81591', 'nombre' => 'Saravena', 'departamento' => 'Arauca', 'codigo_departamento' => '81', 'region' => 'Orinoquía', 'capital_departamento' => false],

            // CAQUETÁ
            ['codigo_dane' => '18001', 'nombre' => 'Florencia', 'departamento' => 'Caquetá', 'codigo_departamento' => '18', 'region' => 'Amazonía', 'capital_departamento' => true],
            ['codigo_dane' => '18247', 'nombre' => 'El Doncello', 'departamento' => 'Caquetá', 'codigo_departamento' => '18', 'region' => 'Amazonía', 'capital_departamento' => false],
            ['codigo_dane' => '18592', 'nombre' => 'Puerto Rico', 'departamento' => 'Caquetá', 'codigo_departamento' => '18', 'region' => 'Amazonía', 'capital_departamento' => false],

            // PUTUMAYO
            ['codigo_dane' => '86001', 'nombre' => 'Mocoa', 'departamento' => 'Putumayo', 'codigo_departamento' => '86', 'region' => 'Amazonía', 'capital_departamento' => true],
            ['codigo_dane' => '86568', 'nombre' => 'Puerto Asís', 'departamento' => 'Putumayo', 'codigo_departamento' => '86', 'region' => 'Amazonía', 'capital_departamento' => false],
            ['codigo_dane' => '86749', 'nombre' => 'Valle del Guamuez', 'departamento' => 'Putumayo', 'codigo_departamento' => '86', 'region' => 'Amazonía', 'capital_departamento' => false],

            // AMAZONAS
            ['codigo_dane' => '91001', 'nombre' => 'Leticia', 'departamento' => 'Amazonas', 'codigo_departamento' => '91', 'region' => 'Amazonía', 'capital_departamento' => true],
            ['codigo_dane' => '91263', 'nombre' => 'El Encanto', 'departamento' => 'Amazonas', 'codigo_departamento' => '91', 'region' => 'Amazonía', 'capital_departamento' => false],
            ['codigo_dane' => '91460', 'nombre' => 'Puerto Nariño', 'departamento' => 'Amazonas', 'codigo_departamento' => '91', 'region' => 'Amazonía', 'capital_departamento' => false],

            // GUAINÍA
            ['codigo_dane' => '94001', 'nombre' => 'Inírida', 'departamento' => 'Guainía', 'codigo_departamento' => '94', 'region' => 'Amazonía', 'capital_departamento' => true],
            ['codigo_dane' => '94343', 'nombre' => 'Barranco Minas', 'departamento' => 'Guainía', 'codigo_departamento' => '94', 'region' => 'Amazonía', 'capital_departamento' => false],

            // GUAVIARE
            ['codigo_dane' => '95001', 'nombre' => 'San José del Guaviare', 'departamento' => 'Guaviare', 'codigo_departamento' => '95', 'region' => 'Amazonía', 'capital_departamento' => true],
            ['codigo_dane' => '95015', 'nombre' => 'Calamar', 'departamento' => 'Guaviare', 'codigo_departamento' => '95', 'region' => 'Amazonía', 'capital_departamento' => false],

            // VAUPÉS
            ['codigo_dane' => '97001', 'nombre' => 'Mitú', 'departamento' => 'Vaupés', 'codigo_departamento' => '97', 'region' => 'Amazonía', 'capital_departamento' => true],
            ['codigo_dane' => '97161', 'nombre' => 'Carurú', 'departamento' => 'Vaupés', 'codigo_departamento' => '97', 'region' => 'Amazonía', 'capital_departamento' => false],

            // VICHADA
            ['codigo_dane' => '99001', 'nombre' => 'Puerto Carreño', 'departamento' => 'Vichada', 'codigo_departamento' => '99', 'region' => 'Orinoquía', 'capital_departamento' => true],
            ['codigo_dane' => '99524', 'nombre' => 'La Primavera', 'departamento' => 'Vichada', 'codigo_departamento' => '99', 'region' => 'Orinoquía', 'capital_departamento' => false],

            // SAN ANDRÉS Y PROVIDENCIA
            ['codigo_dane' => '88001', 'nombre' => 'San Andrés', 'departamento' => 'San Andrés y Providencia', 'codigo_departamento' => '88', 'region' => 'Insular', 'capital_departamento' => true],
            ['codigo_dane' => '88564', 'nombre' => 'Providencia', 'departamento' => 'San Andrés y Providencia', 'codigo_departamento' => '88', 'region' => 'Insular', 'capital_departamento' => false],

            // CHOCÓ
            ['codigo_dane' => '27001', 'nombre' => 'Quibdó', 'departamento' => 'Chocó', 'codigo_departamento' => '27', 'region' => 'Pacífica', 'capital_departamento' => true],
            ['codigo_dane' => '27025', 'nombre' => 'Acandí', 'departamento' => 'Chocó', 'codigo_departamento' => '27', 'region' => 'Pacífica', 'capital_departamento' => false],
            ['codigo_dane' => '27361', 'nombre' => 'Istmina', 'departamento' => 'Chocó', 'codigo_departamento' => '27', 'region' => 'Pacífica', 'capital_departamento' => false],
        ];

        // Agregar timestamps a cada registro
        foreach ($municipios as &$municipio) {
            $municipio['created_at'] = now();
            $municipio['updated_at'] = now();
        }

        DB::table('cat_municipios_colombia')->insert($municipios);
    }
}
