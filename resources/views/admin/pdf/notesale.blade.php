<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        @page{
            margin: 12mm;
        }

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:11px;
            color:#222;
            margin:0;
            padding:0;
        }

        .page{
            width:94%;          /* o 92% si quieres aún más margen */
            margin:0 auto;      /* centra el contenido */
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        td,
        th{
            padding:5px;
            vertical-align:top;
        }

        .border{
            border:1px solid #000;
        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .title{
            font-size:22px;
            font-weight:bold;
        }

        .subtitle{
            font-size:13px;
            font-weight:bold;
        }

        .small{
            font-size:10px;
            line-height:14px;
        }

        .header{
            margin-bottom:15px;
        }

        .note-box{
            width:100%;
            border-collapse:collapse;
        }

        .note-box td{
            border:1px solid #000;
            padding:5px;
        }

        .client{
            margin-top:10px;
            margin-bottom:15px;
        }

        .client td{
            border:1px solid #000;
        }

        .products{
            margin-top:10px;
        }

        .products th{
            background:#efefef;
            border:1px solid #000;
        }

        .products td{
            border:1px solid #000;
        }

        .totals{
            margin-top:12px;
        }

        .totals td{
            border:1px solid #000;
        }

        .footer{
            margin-top:18px;
            font-size:10px;
            text-align:justify;
            line-height:15px;
        }
    </style>
</head>

<body>

<div class="page">

<table class="header">

<tr>

    <td width="18%" class="border text-center">
        <img style="width: 160px;display: block;margin: 30px auto;" src="{{ url('img/simonel.png')}}" alt="">
    </td>

    <td width="57%" class="text-center border">

        <div class="title">
            SIMONEL
        </div>

        <div class="subtitle">
            PROFR. SIMON MOLINA MALDONADO
        </div>

        <br>

        <div class="small">
            RFC: MOMS-460704-aQ9
        </div>

        <table style="width:100%; margin-top:8px; border-collapse:collapse;">
            <tr>
                <td class="small" style="width:50%; text-align:left;">
                    Calle Río Tamazula 210
                </td>
                <td class="small" style="width:50%; text-align:left;">
                    Col. Gustavo Díaz Ordaz
                </td>
            </tr>

            <tr>
                <td class="small" style="width:50%; text-align:left;">
                    TEL. (618) 143-19-84
                </td>
                <td class="small" style="width:50%; text-align:left;">
                    CEL. (618) 171-95-12
                </td>
            </tr>
        </table>

    </td>

    <td width="25%">

        <table class="note-box">

            <tr>
                <td colspan="2" class="text-center">
                    <strong>NOTA DE VENTA</strong>
                </td>
            </tr>

            <tr>
                <td width="40%">
                    Folio
                </td>

                <td class="text-center">
                    <strong>#{{ $sale->id }}</strong>
                </td>
            </tr>

            <tr>
                <td>Fecha</td>

                <td class="text-center">
                    {{ $sale->created_at->format('d/m/Y') }}
                </td>

            </tr>

        </table>

    </td>

</tr>

</table>

<table class="client">

<tr>

    <td width="20%">
        <strong>NOMBRE</strong>
    </td>

    <td width="80%">
        {{ optional($sale->user->customer)->business_name }}
    </td>

</tr>
<tr>

    <td>
        <strong>DIRECCIÓN</strong>
    </td>

    <td>
        {{ optional($sale->user->customer)->street }} #{{ optional($sale->user->customer)->ext_number }}, {{ optional($sale->user->customer)->population }} {{ optional($sale->user->customer)->postal_code }} 
    </td>

</tr>
<tr>

    <td>
        <strong>CIUDAD</strong>
    </td>

    <td>
        {{ optional($sale->user->customer)->state }}
    </td>

</tr>

</table>

<table class="products">

<thead>

<tr>

    <th width="10%">
        Cant.
    </th>

    <th width="42%">
        Producto
    </th>

    <th width="16%">
        Precio Unit.
    </th>

    <th width="12%">
        Desc.
    </th>

    <th width="20%">
        Importe
    </th>

</tr>

</thead>

<tbody>
    @foreach($sale->products as $product)

<tr>

    <td class="text-center">
        {{ number_format($product->quantity,2) }}
    </td>

    <td>
        {{ optional(optional($product->product)->manufactured)->name }}
    </td>

    <td class="text-right">
        $ {{ number_format($product->base_price,2) }}
    </td>

    <td class="text-center">
        {{ number_format($product->discount,2) }} %
    </td>

    <td class="text-right">
        $ {{ number_format($product->subtotal,2) }}
    </td>

</tr>

@endforeach


@php
    $rows = max(0, 7 - $sale->products->count());
@endphp

@for($i = 0; $i < $rows; $i++)

<tr>

    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>

</tr>

@endfor

</tbody>

</table>


<table class="totals">

<tr>

    <td width="70%">

        <strong>Cantidad con letra</strong>

        <br><br>

        {{ $sale->letter }}

        @if($sale->comment)

        <br><br>

        <strong>Comentarios:</strong>

        {{ $sale->comment }}

        @endif

    </td>

    <td width="30%">

        <table style="width:100%; border-collapse:collapse;">

            <tr>

                <td>
                    Subtotal
                </td>

                <td class="text-right">
                    $ {{ number_format($sale->gross_amount,2) }}
                </td>

            </tr>

            <tr>

                <td>
                    Descuento
                </td>

                <td class="text-right">
                    - $ {{ number_format($sale->discount,2) }}
                </td>

            </tr>

            <tr>

                <td style="font-size:15px;">

                    <strong>TOTAL</strong>

                </td>

                <td class="text-right" style="font-size:16px;">

                    <strong>

                        $ {{ number_format($sale->total_sale_price,2) }}

                    </strong>

                </td>

            </tr>

        </table>

    </td>

</tr>

</table>
<table style="width:100%; margin-top:15px; border-collapse:collapse;">

    <tr>
        <td colspan="2" style="border:1px solid #000; background:#efefef;">
            <strong>Detalle de pagos</strong>
        </td>
    </tr>


    @foreach($sale->payments as $payment)

    <tr>

        <td style="border:1px solid #000; width:70%;">
            {{ $payment->name }}
        </td>

        <td style="border:1px solid #000; width:30%; text-align:right;">
            $ {{ number_format($payment->pivot->cost,2) }}
        </td>

    </tr>

    @endforeach

</table>
@php
    $credito = $sale->payments->firstWhere('key_name', 'credito-simonel');
    $saldoCredito = $credito ? $credito->pivot->cost : 0;
@endphp

@if($credito)


<div class="footer">

    <p style="text-align:justify; line-height:16px;">

        Por este pagaré me obligo(amos) a pagar incondicionalmente la cantidad de

        <strong>
            $ {{ number_format($saldoCredito,2) }}
        </strong>

        a favor de <strong>SIMONEL</strong>,
        reconociendo haber recibido la mercancía descrita anteriormente a mi
        entera satisfacción.

        En caso de incumplimiento, acepto(amos) cubrir los intereses y gastos
        que correspondan hasta la liquidación total del adeudo.

    </p>

</div>

<table style="width:100%; margin-top:55px;">

    <tr>
        <td width="50%" class="text-center">
            ______________________________
            <br>
            <strong>ACEPTO</strong>
        </td>
    </tr>
</table>
@endif

<div style="margin-top:25px; text-align:center; font-size:9px; color:#666;">
    Documento generado automáticamente el
    {{ now()->format('d/m/Y H:i') }}
</div>

</div>

</body>

</html>