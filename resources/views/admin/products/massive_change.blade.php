@extends('admin.master')

@section('title', 'Cambiar precios masivamente')

@section('breadcrumb')
<li class="breadcrumb-item">
	<a href="{{ url('/admin/products') }}"><i class="fas fa-boxes"></i> Productos</a>
</li>
<li class="breadcrumb-item">
	<a href="{{ url('/admin/products/massive/change') }}"><i class="fas fa-refresh"></i> Cambiar precios masivamente</a>
</li>
@endsection

<style>
	.custom_input{
		padding: 10px 20px;
		box-shadow: 1px 1px 12px 1px rgba(0,0,0,.2);
		border: 1px solid rgba(0,0,0,.4);
		border-radius: 50px;
	}
</style>

@section('content')
<div class="container-fluid">
	<div class="panel shadow">
		<div class="header">
			<h2 class="title"><i class="fas fa-refresh"></i> Cambiar precios masivamente</h2>
		</div>

		<div class="inside">
			
			<div class="row">
				<div class="col-md-12">
					<button class="btn btn-primary btn-sm" data-toggle="modal" data-target=".bd-example-modal-lg">
						<i class="fa fa-hand-pointer-o text-light"></i> 
						Seleccione productos
					</button>
				</div>
			</div>

			<div class="row mtop16">
				<form action="{{ route('postMassiveChange') }}" method="POST">
					@csrf
					<input type="hidden" name="arreglo_id[]" id="arreglo_id">
					<table class="table w-100" id="table_home" style="overflow-x: scroll;">
						<thead class="thead-dark bg-dark text-light">
						<tr>
							<th>Producto</th>
							<th>Precio</th>
							<th>Nuevo Precio</th>
						</tr>
						</thead>
						<tbody id="tabla_cambios">
							
						</tbody>
					</table>
				<form >
			</div>

			<div class="row mtop16">
				<div class="col-md-12">
					<button class="btn btn-success btn-md" id="btn_submit">
						<i class="fa fa-floppy-o"></i>
						Guardar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade bd-example-modal-lg" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title" id="exampleModalLongTitle">Productos</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<table class="table" id="table" style="overflow-x: scroll;">
				<thead class="thead-dark bg-dark text-light">
				  <tr>
					<th>Cod. Sistema</th>
					<th>Producto</th>
					<th>Precio</th>
					<th>Acción</th>
				  </tr>
				</thead>
				<tbody>
					@isset($products)
						@foreach($products as $product)
							<tr>
								<th scope="row">{{ $product->code == '0' ? '-' : $product->code }}</th>
								<td><b>{{ $product->name }}</b></td>
								<td><b>$ {{ number_format($product->price, 0, '', '.') }}</b></td>
								<td>
									<button class="btn btn-primary btn-sm" id="button_plus_{{$product->id}}" onclick="agregarProducto({{ $product }})">
										<i class="fa fa-plus text-light"></i>
									</button>

									<button class="btn btn-danger btn-sm d-none" id="button_trash_{{$product->id}}" onclick="quitarProducto({{ $product->id }})">
										<i class="fa fa-trash text-light"></i>
									</button>
								</td>
							</tr>
						@endforeach
					@endisset
				</tbody>
			</table>
		</div>
	  </div>
	</div>
</div>

<script>
	$(document).ready(() => {
		$('#table').DataTable({
			responsive: true,
			language: {
				url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
			},
			"pageLength": 100,
			mark: true,
			"ordering": false
		});

		$("#btn_submit").hide();

		arreglo = [];
	});

	function agregarProducto(arreglo_producto){
		arreglo.push(arreglo_producto.id)
		$("#btn_submit").show();
		$("#button_plus_"+arreglo_producto.id).addClass('d-none');
		$("#button_trash_"+arreglo_producto.id).removeClass('d-none');

		$("#tabla_cambios").append(`
			<tr id="producto_${arreglo_producto.id}">
				<td><b>${arreglo_producto.name}</b></td>
				<td><b>$ ${new Intl.NumberFormat('es-CL').format(arreglo_producto.price)}</b></td>
				<td>
					<input type="number" class="custom_input" value="0" name="new_price_product_${arreglo_producto.id}">
				</td>
			</tr>
		`);
	}

	function quitarProducto(id_producto){
		eliminar = arreglo.findIndex(item => item === id_producto);
		arreglo.splice(eliminar, 1)
		arreglo.length <= 0 ? $("#btn_submit").hide() : ""
		$("#button_trash_"+id_producto).addClass('d-none');
		$("#button_plus_"+id_producto).removeClass('d-none');
		$("#producto_"+id_producto).remove()
	}

	$("#btn_submit").click(() => {
		$("#arreglo_id").val(arreglo)
	})
</script>
@endsection
