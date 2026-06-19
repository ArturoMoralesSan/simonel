<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Etiqueta {{ $lot->lot_number }}</title>

    <style>

        body{
            font-family: Arial, Helvetica, sans-serif;
            margin:0;
            padding:20px;
            background:#fff;
        }

        .label{
            width:400px;
            margin:auto;
            border:1px solid #000;
            padding:15px;
            box-sizing:border-box;
        }

        .company{
            text-align:center;
            font-size:22px;
            font-weight:bold;
            margin-bottom:10px;
        }

        .product{
            text-align:center;
            font-size:18px;
            font-weight:bold;
            margin-bottom:15px;
        }

        .field{
            margin-bottom:6px;
            font-size:14px;
        }

        .field strong{
            display:inline-block;
            width:120px;
        }


        .qr{
            margin-top:20px;
            text-align:center;
        }

        .footer{
            margin-top:10px;
            text-align:center;
            font-size:12px;
        }
        .barcode{
            margin-top:20px;
            text-align:center;
        }

        .barcode img{
            display:block;
            margin:auto;
            max-width:100%;
        }
        
        @media print {

            .barcode img{
                max-width:100%;
                page-break-inside:avoid;
            }

            body{
                padding:0;
            }

            .label{
                border:none;
            }
        }

    </style>

</head>
<body>

<div class="label">

    <div class="company">
        SIMONEL
    </div>

    <div class="product">
        {{ $lot->product->manufactured->name }}
    </div>

    <div class="field">
        <strong>Lote:</strong>
        {{ $lot->lot_number }}
    </div>

    <div class="field">
        <strong>Producción:</strong>
        {{ \Carbon\Carbon::parse($lot->production_date)->format('d/m/Y') }}
    </div>

    <div class="field">
        <strong>Caducidad:</strong>
        {{ \Carbon\Carbon::parse($lot->expiration_date)->format('d/m/Y') }}
    </div>

    <div class="field">
        <strong>Cantidad:</strong>
        {{ $lot->initial_quantity }}
    </div>

    <div class="field">
        <strong>Disponible:</strong>
        {{ $lot->available_quantity }}
    </div>

    <div class="barcode">

        <img
        src="data:image/png;base64,{{ DNS1D::getBarcodePNG(
            $lot->lot_number,
            'C128',
            2,
            60
        ) }}"
        alt="Código de barras"
        style="
            max-width:100%;
            height:auto;
        "
    >

    <div style="
        margin-top:10px;
        font-size:14px;
        font-weight:bold;
        letter-spacing:2px;
    ">
        {{ $lot->lot_number }}
    </div>


    </div>

    <div class="qr">

        {!! QrCode::size(140)->generate(
            url('admin/lotes-producto/'.$lot->id.'/detalle')
        ) !!}

    </div>

    <div class="footer">
        Escanee el QR para consultar el detalle del lote.
    </div>

</div>

<script>
    window.print();
</script>

</body>
</html>