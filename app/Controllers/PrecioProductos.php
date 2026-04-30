<?php

namespace App\Controllers;

use App\Models\PrecioProductoModel;
use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class PrecioProductos extends BaseController
{
    public function index()
    {
        $precioProductoModel = new PrecioProductoModel();
        $productoModel = new ProductoModel();
        $categoriaModel = new CategoriaModel();

        $buscar = trim((string) $this->request->getGet('buscar'));
        $molino = trim((string) $this->request->getGet('molino'));
        $categoriaId = trim((string) $this->request->getGet('categoria_id'));

        $precios = $this->obtenerPreciosFiltrados($precioProductoModel, $buscar, $molino, $categoriaId);

        $molinos = $productoModel
            ->select('molino')
            ->where('molino IS NOT NULL')
            ->where('molino !=', '')
            ->groupBy('molino')
            ->orderBy('molino', 'ASC')
            ->findAll();

        $categorias = $categoriaModel
            ->orderBy('nombre', 'ASC')
            ->findAll();

        return view('precio_productos/index', [
            'precios'     => $precios,
            'buscar'      => $buscar,
            'molino'      => $molino,
            'categoriaId' => $categoriaId,
            'molinos'     => $molinos,
            'categorias'  => $categorias,
        ]);
    }

    public function pdf()
    {
        ini_set('memory_limit', '512M');

        $precioProductoModel = new PrecioProductoModel();
        $categoriaModel = new CategoriaModel();

        $buscar = trim((string) $this->request->getGet('buscar'));
        $molino = trim((string) $this->request->getGet('molino'));
        $categoriaId = trim((string) $this->request->getGet('categoria_id'));

        $precios = $this->obtenerPreciosFiltrados($precioProductoModel, $buscar, $molino, $categoriaId);
        $categoriaSeleccionada = $categoriaId !== '' ? $categoriaModel->find($categoriaId) : null;
        $preciosAgrupados = $this->prepararPreciosParaPdf($precios);

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        $options->setChroot(FCPATH);

        $tempDir = WRITEPATH . 'dompdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $options->setTempDir($tempDir);

        $dompdf = new Dompdf($options);

        $html = view('pdf/lista_precios', [
            'preciosAgrupados' => $preciosAgrupados,
            'filtros' => [
                'buscar'    => $buscar,
                'molino'    => $molino,
                'categoria' => $categoriaSeleccionada['nombre'] ?? '',
            ],
            'fechaDocumento' => date('d/m/Y'),
            'empresaNombre'   => 'GP',
            'logoPath'        => $this->obtenerLogoListaPrecios(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="lista-precios.pdf"')
            ->setBody($dompdf->output());
    }

    public function create()
    {
        if (session('rol') !== 'admin') {
            return redirect()->to('/precio-productos')->with('error', 'No tenés permiso para crear precios.');
        }

        $productoModel = new ProductoModel();

        $productos = $productoModel
            ->select('productos.*, categorias.nombre AS categoria_nombre')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->orderBy('productos.nombre', 'ASC')
            ->findAll();

        return view('precio_productos/create', [
            'productos' => $productos,
            'modo'      => 'crear',
            'precio'    => null,
        ]);
    }

    public function store()
    {
        if (session('rol') !== 'admin') {
            return redirect()->to('/precio-productos')->with('error', 'No tenés permiso para crear precios.');
        }

        $rules = [
            'producto_id'      => 'required|is_not_unique[productos.id]',
            'cantidad_desde'   => 'required|integer|greater_than[0]',
            'cantidad_hasta'   => 'permit_empty|integer|greater_than[0]',
            'precio_unitario'  => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $cantidadDesde = (int) $this->request->getPost('cantidad_desde');
        $cantidadHastaPost = $this->request->getPost('cantidad_hasta');
        $cantidadHasta = ($cantidadHastaPost === '' || $cantidadHastaPost === null) ? null : (int) $cantidadHastaPost;

        if ($cantidadHasta !== null && $cantidadHasta < $cantidadDesde) {
            return redirect()->back()->withInput()->with('error', 'La cantidad hasta no puede ser menor que la cantidad desde.');
        }

        $precioProductoModel = new PrecioProductoModel();

        $precioProductoModel->save([
            'producto_id'      => $this->request->getPost('producto_id'),
            'cantidad_desde'   => $cantidadDesde,
            'cantidad_hasta'   => $cantidadHasta,
            'precio_unitario'  => $this->request->getPost('precio_unitario'),
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/precio-productos')->with('success', 'Precio cargado correctamente.');
    }

    public function edit($id)
    {
        if (session('rol') !== 'admin') {
            return redirect()->to('/precio-productos')->with('error', 'No tenés permiso para editar precios.');
        }

        $precioProductoModel = new PrecioProductoModel();
        $productoModel = new ProductoModel();

        $precio = $precioProductoModel->find($id);

        if (!$precio) {
            return redirect()->to('/precio-productos')->with('error', 'El precio no existe.');
        }

        $productos = $productoModel
            ->select('productos.*, categorias.nombre AS categoria_nombre')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->orderBy('productos.nombre', 'ASC')
            ->findAll();

        return view('precio_productos/create', [
            'productos' => $productos,
            'modo'      => 'editar',
            'precio'    => $precio,
        ]);
    }

    public function update($id)
    {
        if (session('rol') !== 'admin') {
            return redirect()->to('/precio-productos')->with('error', 'No tenés permiso para editar precios.');
        }

        $precioProductoModel = new PrecioProductoModel();
        $precioActual = $precioProductoModel->find($id);

        if (!$precioActual) {
            return redirect()->to('/precio-productos')->with('error', 'El precio no existe.');
        }

        $rules = [
            'producto_id'     => 'required|is_not_unique[productos.id]',
            'cantidad_desde'  => 'required|integer|greater_than[0]',
            'cantidad_hasta'  => 'permit_empty|integer|greater_than[0]',
            'tipo_edicion'    => 'required|in_list[final,monto,porcentaje]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $cantidadDesde = (int) $this->request->getPost('cantidad_desde');
        $cantidadHastaPost = $this->request->getPost('cantidad_hasta');
        $cantidadHasta = ($cantidadHastaPost === '' || $cantidadHastaPost === null) ? null : (int) $cantidadHastaPost;

        if ($cantidadHasta !== null && $cantidadHasta < $cantidadDesde) {
            return redirect()->back()->withInput()->with('error', 'La cantidad hasta no puede ser menor que la cantidad desde.');
        }

        $tipoEdicion = $this->request->getPost('tipo_edicion');
        $precioBase = (float) $precioActual['precio_unitario'];
        $nuevoPrecio = 0;

        if ($tipoEdicion === 'final') {
            $precioFinal = $this->request->getPost('precio_final');

            if ($precioFinal === null || $precioFinal === '' || !is_numeric($precioFinal) || (float) $precioFinal <= 0) {
                return redirect()->back()->withInput()->with('error', 'Debés ingresar un precio final válido y mayor a 0.');
            }

            $nuevoPrecio = (float) $precioFinal;
        }

        if ($tipoEdicion === 'monto') {
            $ajusteMonto = $this->request->getPost('ajuste_monto');

            if ($ajusteMonto === null || $ajusteMonto === '' || !is_numeric($ajusteMonto)) {
                return redirect()->back()->withInput()->with('error', 'Debés ingresar un monto válido. Puede ser positivo o negativo.');
            }

            $nuevoPrecio = $precioBase + (float) $ajusteMonto;
        }

        if ($tipoEdicion === 'porcentaje') {
            $ajustePorcentaje = $this->request->getPost('ajuste_porcentaje');

            if ($ajustePorcentaje === null || $ajustePorcentaje === '' || !is_numeric($ajustePorcentaje)) {
                return redirect()->back()->withInput()->with('error', 'Debés ingresar un porcentaje válido. Puede ser positivo o negativo.');
            }

            $nuevoPrecio = $precioBase + ($precioBase * ((float) $ajustePorcentaje / 100));
        }

        if ($nuevoPrecio <= 0) {
            return redirect()->back()->withInput()->with('error', 'El precio resultante debe ser mayor a 0.');
        }

        $precioProductoModel->update($id, [
            'producto_id'      => $this->request->getPost('producto_id'),
            'cantidad_desde'   => $cantidadDesde,
            'cantidad_hasta'   => $cantidadHasta,
            'precio_unitario'  => round($nuevoPrecio, 2),
        ]);

        return redirect()->to('/precio-productos')->with('success', 'Precio actualizado correctamente.');
    }

    private function obtenerPreciosFiltrados(PrecioProductoModel $precioProductoModel, string $buscar, string $molino, string $categoriaId): array
    {
        $builder = $precioProductoModel
            ->select('precio_productos.*, productos.nombre AS producto_nombre, productos.kilogramos, productos.molino, categorias.nombre AS categoria_nombre, categorias.id AS categoria_id')
            ->join('productos', 'productos.id = precio_productos.producto_id')
            ->join('categorias', 'categorias.id = productos.categoria_id');

        if ($buscar !== '') {
            $builder->like('productos.nombre', $buscar);
        }

        if ($molino !== '') {
            $builder->where('productos.molino', $molino);
        }

        if ($categoriaId !== '') {
            $builder->where('productos.categoria_id', $categoriaId);
        }

        return $builder
            ->orderBy('productos.nombre', 'ASC')
            ->orderBy('precio_productos.cantidad_desde', 'ASC')
            ->findAll();
    }

    private function prepararPreciosParaPdf(array $precios): array
    {
        $productos = [];

        foreach ($precios as $precio) {
            $productoId = (int) ($precio['producto_id'] ?? 0);

            if ($productoId <= 0) {
                continue;
            }

            if (!isset($productos[$productoId])) {
                $productos[$productoId] = [
                    'producto_nombre' => $precio['producto_nombre'] ?? '-',
                    'molino'          => $precio['molino'] ?: 'Sin molino',
                    'rangos'          => [],
                ];
            }

            $productos[$productoId]['rangos'][] = $precio;
        }

        $agrupados = [];

        foreach ($productos as $producto) {
            $molino = $producto['molino'] ?: 'Sin molino';

            if (!isset($agrupados[$molino])) {
                $agrupados[$molino] = [
                    'molino'    => $molino,
                    'productos' => [],
                ];
            }

            $agrupados[$molino]['productos'][] = [
                'producto_nombre' => $producto['producto_nombre'],
                'mas_10'          => $this->resolverPrecioPorCantidad($producto['rangos'], 11),
                'hasta_50'        => $this->resolverPrecioPorCantidad($producto['rangos'], 50),
                'mas_50'          => $this->resolverPrecioPorCantidad($producto['rangos'], 51),
            ];
        }

        ksort($agrupados, SORT_NATURAL | SORT_FLAG_CASE);

        foreach ($agrupados as &$grupo) {
            usort($grupo['productos'], static function (array $a, array $b): int {
                return strcasecmp($a['producto_nombre'], $b['producto_nombre']);
            });
        }

        unset($grupo);

        return array_values($agrupados);
    }

    private function resolverPrecioPorCantidad(array $rangos, int $cantidad): ?float
    {
        foreach ($rangos as $rango) {
            $desde = (int) ($rango['cantidad_desde'] ?? 0);
            $hasta = $rango['cantidad_hasta'] !== null ? (int) $rango['cantidad_hasta'] : null;

            if ($cantidad >= $desde && ($hasta === null || $cantidad <= $hasta)) {
                return (float) ($rango['precio_unitario'] ?? 0);
            }
        }

        return null;
    }

    private function obtenerLogoListaPrecios(): string
    {
        $rutaLogo = FCPATH . 'img/logo-gp.png';

        if (!is_file($rutaLogo)) {
            return '';
        }

        $realPath = realpath($rutaLogo);

        if ($realPath === false) {
            return '';
        }

        return 'file:///' . str_replace('\\', '/', $realPath);
    }
}
