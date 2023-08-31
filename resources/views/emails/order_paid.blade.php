@extends('emails.master')

@section('content')
<p>Hola: <strong>{{ $name }}</strong></p>
<p>El pago de su orden #{{ $order }} fue recibido correctamente.</p>

<p>Muchas gracias por confiar en nuestros productos.</p>

@stop