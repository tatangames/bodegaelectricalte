@extends('adminlte::page')

@section('title', 'Retiro de Material — Proyectos Cerrados')

@section('content_header')
    <h1>Retiro de Material — Proyectos Cerrados</h1>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Sweetalert2', true)

@include('backend.urlglobal')

@section('content_top_nav_right')
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">

    <li class="nav-item dropdown">
        <a href="#" class="nav-link" data-toggle="dropdown">
            <i class="fas fa-cogs"></i>
            <span class="d-none d-md-inline">{{ Auth::guard('admin')->user()->nombre }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a href="{{ route('admin.perfil') }}" class="dropdown-item">
                <i class="fas fa-user mr-2"></i>Editar Perfil
            </a>
        </div>
    </li>
    <li class="nav-item">
        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="nav-link btn btn-link border-0 bg-transparent">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-none d-md-inline">Cerrar Sesión</span>
            </button>
        </form>
    </li>
@endsection

@section('content')
    <style>
        table { table-layout: fixed; }
        *:focus { outline: none; }

        .seccion-header {
            background: linear-gradient(135deg, #1a3a6b 0%, #2156af 100%);
            border-radius: 10px 10px 0 0;
            padding: 12px 18px;
        }
        .seccion-header h3 {
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin: 0;
        }
        .card-info {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 18px rgba(33,86,175,.13);
            margin-bottom: 20px;
        }
        .field-label {
            font-size: 11px;
            font-weight: 700;
            color: #6b7a99;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 5px;
            display: block;
        }
        .divider-azul {
            border: none;
            border-top: 2px solid #e8eef8;
            margin: 18px 0;
        }
        #matriz thead tr th {
            background: #2156af;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border: none !important;
            padding: 10px 12px;
        }
        #matriz tbody td { vertical-align: middle; font-size: 13px; padding: 8px 10px; }
        .btn-guardar-salida {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            font-weight: 400;
            font-size: 14px;
            box-shadow: 0 4px 14px rgba(40,167,69,.35);
            transition: all .2s;
        }
        .btn-guardar-salida:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(40,167,69,.45);
            color: #fff;
        }
    </style>

    <div id="divcontenedor" style="display:none">

        {{-- ══ INFORMACIÓN ══ --}}
        <section class="content" style="margin-bottom:0">
            <div class="container-fluid">
                <div class="card card-info">

                    <div class="seccion-header">
                        <h3><i class="fas fa-info-circle mr-2"></i>Información de Retiro</h3>
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="field-label"><i class="fas fa-calendar-alt mr-1"></i>Fecha</label>
                                    <input type="date" class="form-control" id="fecha">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="field-label">
                                        <i class="fas fa-lock mr-1"></i>Proyecto Cerrado
                                    </label>
                                    <select class="form-control" id="select-proyecto">
                                        <option value="0" selected disabled>Seleccionar Proyecto Cerrado</option>
                                        @foreach($proyectosCerrados as $item)
                                            <option value="{{ $item->id }}">{{ $item->id }} — {{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="divider-azul">

                        <div class="row align-items-end">
                            <div class="col-md-8 mb-2">
                                <label class="field-label">
                                    <i class="fas fa-align-left mr-1"></i>Descripción
                                    <small style="text-transform:none; font-weight:400">(Opcional)</small>
                                </label>
                                <input type="text" class="form-control" autocomplete="off"
                                       maxlength="800" id="descripcion" placeholder="Motivo del retiro…">
                            </div>
                            <div class="col-md-4 mb-2 d-flex justify-content-end">
                                <button type="button" id="botonaddmaterial" onclick="abrirModal()"
                                        class="btn btn-primary btn-sm" disabled
                                        style="border-radius:6px; font-weight:400">
                                    <i class="fas fa-search mr-1"></i> Buscar Material
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        {{-- ══ DETALLE ══ --}}
        <section class="content">
            <div class="container-fluid">
                <div class="card card-info">

                    <div class="seccion-header" style="display:flex; justify-content:space-between; align-items:center">
                        <h3><i class="fas fa-list mr-2"></i>Detalle de Retiro</h3>
                        <span id="contador-filas"
                              style="background:rgba(255,255,255,.2); color:#fff; border-radius:20px; padding:2px 12px; font-size:12px; font-weight:700">
                        0 ítems
                    </span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0" id="matriz"
                                   style="table-layout:fixed; width:100%">
                                <thead>
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:45%">Material</th>
                                    <th style="width:15%">Cantidad</th>
                                    <th style="width:10%">Opciones</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center"
                         style="border-top:2px solid #e8eef8; background:#f8faff; border-radius:0 0 10px 10px">
                        <small class="text-muted">Seleccione un proyecto cerrado y agregue materiales</small>
                        <button type="button" class="btn-guardar-salida" onclick="preguntaGuardar()">
                            <i class="fas fa-save mr-1"></i>Guardar Retiro
                        </button>
                    </div>

                </div>
            </div>
        </section>

        {{-- ══ MODAL: BUSCAR MATERIAL ══ --}}
        <div class="modal fade" id="modalRepuesto">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header" style="background:#2156af">
                        <h4 class="modal-title" style="color:#fff">
                            <i class="fas fa-search mr-2"></i>Buscar Material del Proyecto
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" style="color:#fff">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="field-label">
                                    Material — Solo muestra inventario disponible del proyecto cerrado
                                    <span class="badge badge-warning ml-1">Proyecto Cerrado</span>
                                </label>
                                <table class="table" id="matriz-busqueda">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input id="inputBuscador" autocomplete="off"
                                                   class="form-control" style="width:100%"
                                                   onkeyup="buscarMaterial(this)"
                                                   maxlength="300" type="text"
                                                   placeholder="Escribir nombre del material…">
                                            <div class="droplista" id="midropmenu"
                                                 style="position:absolute; z-index:9; width:95%"></div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="tablaRepuesto"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ MODAL: CANTIDAD ══ --}}
        <div class="modal fade" id="modalCantidad">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header" style="background:#1a3a6b">
                        <h4 class="modal-title" style="color:#fff">
                            <i class="fas fa-boxes mr-2"></i>Retiro de Material
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" style="color:#fff">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" id="id-material-seleccionado">
                                    <div class="form-row mb-3">
                                        <div class="col-md-9">
                                            <label class="field-label">Material</label>
                                            <input type="text" disabled class="form-control" id="info-material">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="field-label">U/M</label>
                                            <input type="text" disabled class="form-control" id="info-medida">
                                        </div>
                                    </div>
                                    <hr class="divider-azul">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="matrizM">
                                            <thead>
                                            <tr>
                                                <th>Fecha Ingreso</th>
                                                <th>Valor</th>
                                                <th>Cant. Disponible</th>
                                                <th>Cant. Retiro</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success"
                                style="font-weight:400; border-radius:6px"
                                onclick="agregarAlDetalle()">
                            <i class="fas fa-plus mr-1"></i> Agregar al Detalle
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- fin #divcontenedor --}}

@stop

@section('js')
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-input-spinner.js') }}"></script>
    <script src="{{ asset('js/custom-editors.js') }}"></script>

    <script>
        $(document).ready(function () {
            document.getElementById("divcontenedor").style.display = "block";

            var hoy = new Date();
            document.getElementById('fecha').value = hoy.toJSON().slice(0, 10);

            window.seguroBuscador = true;

            $(document).click(function () { $(".droplista").hide(); });

            $('#select-proyecto').select2({
                theme: "bootstrap-5",
                language: { noResults: function () { return "Búsqueda no encontrada"; } }
            });

            $('#select-proyecto').on('change', function () {
                var val = $(this).val();
                $('#botonaddmaterial').prop('disabled', !val || val === '0');
                $('#matriz tbody tr').remove();
                actualizarContador();
                $('#select-proyecto').select2('close');
            });
        });

        // ── Abrir modal buscador ──────────────────────────────────────────
        function abrirModal() {
            document.getElementById('tablaRepuesto').innerHTML = "";
            document.getElementById('inputBuscador').value = '';
            $('#modalRepuesto').modal('show');
        }

        // ── Validar teclas numéricas ──────────────────────────────────────
        function validateInput(event) {
            const key = event.key;
            if (["Backspace","ArrowLeft","ArrowRight","Delete","Tab"].includes(key)) return true;
            if (key === "e" || key === "E" || key === "-" || isNaN(Number(key))) return false;
            return true;
        }

        // ── Buscar material del proyecto cerrado ──────────────────────────
        function buscarMaterial(e) {
            if (seguroBuscador) {
                seguroBuscador = false;
                var row        = $(e).closest('tr');
                var texto      = e.value;
                var idProyecto = $('#select-proyecto').val();

                axios.post(urlAdmin + '/admin/buscar/material/disponible', {
                    query:      texto,
                    id_proyecto: idProyecto
                })
                    .then((response) => {
                        seguroBuscador = true;
                        $(row).each(function () {
                            $(this).find(".droplista").fadeIn();
                            $(this).find(".droplista").html(response.data);
                        });
                    })
                    .catch(() => { seguroBuscador = true; });
            }
        }

        // ── Seleccionar material → modal cantidades ───────────────────────
        function modificarValor(edrop) {
            openLoading();
            var formData = new FormData();
            formData.append('id',          edrop.id);
            formData.append('id_proyecto', $('#select-proyecto').val());
            $("#matrizM tbody tr").remove();

            axios.post(urlAdmin + '/admin/buscar/material/disponibilidad', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {

                        if (response.data.disponible === 1) {
                            toastr.info('NO HAY INVENTARIO DISPONIBLE');
                            return;
                        }

                        $('#id-material-seleccionado').val(edrop.id);
                        $('#info-material').val(response.data.nombreMaterial);
                        $('#info-medida').val(response.data.nombreMedida);

                        $.each(response.data.arrayIngreso, function (key, val) {
                            var markup = "<tr>" +
                                "<td><input disabled value='" + val.fechaIngreso + "' class='form-control form-control-sm' type='text'></td>" +
                                "<td><input disabled value='" + val.precioFormat + "' class='form-control form-control-sm' type='text'></td>" +
                                "<td><input name='arrayCantidadActual[]' disabled " +
                                "data-cantidadActualFila='" + val.cantidadActual + "' " +
                                "value='" + val.cantidadActual + "' class='form-control form-control-sm' type='number'></td>" +
                                "<td><input class='form-control form-control-sm' " +
                                "data-idfilaentradadetalle='" + val.id + "' " +
                                "name='arrayCantidadSalida[]' min='0' max='" + val.cantidadActual + "' type='number' " +
                                "onkeydown=\"return validateInput(event);\" " +
                                "oninput=\"validateCantidadSalida(this, " + val.cantidadActual + ");\"></td>" +
                                "</tr>";
                            $("#matrizM tbody").append(markup);
                        });

                        $('#modalCantidad').modal('show');
                    } else {
                        toastr.error('Error al cargar material');
                    }
                })
                .catch(() => { toastr.error('Error'); closeLoading(); });
        }

        // ── Agregar al detalle ────────────────────────────────────────────
        function agregarAlDetalle() {
            var arrayIdEntradaDetalle = $("input[name='arrayCantidadSalida[]']").map(function () { return $(this).attr("data-idfilaentradadetalle"); }).get();
            var arrayCantidadSalida   = $("input[name='arrayCantidadSalida[]']").map(function () { return $(this).val(); }).get();
            var arrayCantidadActual   = $("input[name='arrayCantidadActual[]']").map(function () { return $(this).attr("data-cantidadActualFila"); }).get();

            colorBlancoTabla();
            var habraSalida = true;

            for (var a = 0; a < arrayCantidadSalida.length; a++) {
                var fc  = arrayCantidadSalida[a];
                var max = arrayCantidadActual[a];
                if (fc !== '') {
                    if (fc <= 0) { colorRojoTabla(a); alertaMensaje('info','Error','Fila #'+(a+1)+': No se permite cero'); return; }
                    habraSalida = false;
                }
                if (fc > Number(max)) { colorRojoTabla(a); alertaMensaje('info','Error','Fila #'+(a+1)+': Supera cantidad disponible'); return; }
            }

            if (habraSalida) { toastr.error('Registrar mínimo 1 retiro'); return; }

            var nombreTexto = document.getElementById('info-material').value;
            var nFilas      = $('#matriz >tbody >tr').length;

            for (var z = 0; z < arrayCantidadSalida.length; z++) {
                var fc = arrayCantidadSalida[z];
                if (fc !== '' && fc != 0) {
                    nFilas++;
                    var markup = "<tr>" +
                        "<td><p id='fila" + nFilas + "' class='form-control' style='max-width:55px'>" + nFilas + "</p></td>" +
                        "<td>" +
                        "<input name='idmaterialArray[]' type='hidden' data-idmaterialArray='" + arrayIdEntradaDetalle[z] + "'>" +
                        "<input disabled value='" + nombreTexto + "' class='form-control form-control-sm' type='text'>" +
                        "</td>" +
                        "<td><input name='salidaArray[]' disabled data-cantidadSalida='" + fc + "' value='" + fc + "' class='form-control form-control-sm' type='text'></td>" +
                        "<td><button type='button' class='btn btn-danger btn-block btn-sm' onclick='borrarFila(this)'>Borrar</button></td>" +
                        "</tr>";
                    $("#matriz tbody").append(markup);
                }
            }

            actualizarContador();
            $('#modalCantidad').modal('hide');
            document.getElementById('inputBuscador').value = '';
            toastr.success("Agregado");
        }

        // ── Confirmar guardar ─────────────────────────────────────────────
        function preguntaGuardar() {
            colorBlancoTabla();
            Swal.fire({
                title: '¿Guardar Retiro?',
                text:  'Se registrará la salida del material del proyecto cerrado.',
                icon:  'question',
                showCancelButton:    true,
                confirmButtonColor:  '#28a745',
                cancelButtonColor:   '#d33',
                cancelButtonText:    'Cancelar',
                confirmButtonText:   'Sí, guardar'
            }).then((result) => { if (result.isConfirmed) guardarRetiro(); });
        }

        // ── Guardar retiro ────────────────────────────────────────────────
        function guardarRetiro() {
            var fecha       = document.getElementById('fecha').value;
            var proyecto    = document.getElementById('select-proyecto').value;
            var descripcion = document.getElementById('descripcion').value;

            if (!fecha)                        { toastr.error('Fecha es requerida');           return; }
            if (!proyecto || proyecto === '0') { toastr.error('Seleccione un proyecto');       return; }
            if ($('#matriz > tbody > tr').length <= 0) { toastr.error('Agregue al menos un material'); return; }

            var idEntradaDetalle = $("input[name='idmaterialArray[]']").map(function () { return $(this).attr("data-idmaterialArray"); }).get();
            var salidaCantidad   = $("input[name='salidaArray[]']").map(function ()     { return $(this).attr("data-cantidadSalida"); }).get();

            var contenedorArray = [];
            for (var p = 0; p < salidaCantidad.length; p++) {
                contenedorArray.push({
                    infoIdEntradaDeta: idEntradaDetalle[p],
                    infoCantidad:      salidaCantidad[p],
                });
            }

            openLoading();
            var formData = new FormData();
            formData.append('fecha',            fecha);
            formData.append('proyecto',         proyecto);
            formData.append('descripcion',      descripcion);
            formData.append('contenedorArray',  JSON.stringify(contenedorArray));

            axios.post(urlAdmin + '/admin/transferencia/material/xproyecto', formData)
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastr.error('No hay ítems en el contenedor');
                    } else if (response.data.success === 3) {
                        Swal.fire({
                            title: 'Cantidad no disponible',
                            html:  '<b>' + response.data.nombre_material + '</b><br><br>' +
                                'Solicitado: <b>' + response.data.cantidad_pedida + '</b><br>' +
                                'Disponible: <b>' + response.data.disponible + '</b>',
                            icon:  'warning',
                            confirmButtonColor: '#d33',
                            confirmButtonText:  'Entendido'
                        });
                    } else if (response.data.success === 10) {
                        Swal.fire({
                            title: 'Retiro Registrado',
                            icon:  'success',
                            allowOutsideClick:   false,
                            confirmButtonColor:  '#28a745',
                            confirmButtonText:   'Aceptar'
                        }).then((r) => { if (r.isConfirmed) location.reload(); });
                    } else {
                        toastr.error('Error al guardar');
                    }
                })
                .catch(() => { toastr.error('Error al guardar'); closeLoading(); });
        }

        // ── Utilidades ────────────────────────────────────────────────────
        function borrarFila(elemento) {
            elemento.closest('tr').remove();
            setearFila();
            actualizarContador();
        }

        function setearFila() {
            var table  = document.getElementById('matriz');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo++;
                table.rows[r].cells[0].children[0].innerHTML = conteo;
            }
        }

        function actualizarContador() {
            var n = $('#matriz > tbody > tr').length;
            $('#contador-filas').text(n + (n === 1 ? ' ítem' : ' ítems'));
        }

        function colorRojoTabla(index) {
            $("#matriz tr:eq(" + (index + 1) + ")").css('background', '#f8d7da');
        }

        function colorBlancoTabla() {
            $("#matriz tbody tr").css('background', 'white');
        }

        function validateCantidadSalida(input, maxCantidad) {
            input.value = input.value.replace(/[^0-9]/g, '');
            if (Number(input.value) > maxCantidad) input.value = maxCantidad;
        }
    </script>
@endsection
