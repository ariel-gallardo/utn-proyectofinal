<!DOCTYPE html>
<html lang="es-AR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@100;300&family=Pacifico&display=swap');

        body {

        }

        .titulo {
            font-family: 'Big Shoulders Display', cursive;
            font-size: 5vw;
        }

        .gracias {
            font-family: 'Pacifico', cursive;
        }
        .nombre{
            font-family: 'Pacifico', cursive;
        }
    </style>
    <title>Buen Sabor - Factura</title>
</head>

<body class="d-flex">
    <main class="align-self-center d-flex flex-column justify-content-center mx-auto rounded p-5 bg-secondary rounded">
        <h1 class="text-center text-white text-uppercase titulo">Buen Sabor</h1>
        <p class="text-center text-uppercase text-center text-white"><b>Factura numero° {{$details['numero']}}</b></p>
        <div class="bg-dark p-5 rounded d-flex flex-column justify-content-between">
            <h5 class="text-white gracias">muchas gracias por tu compra</h5>
            <div class="text-center mt-5">
                <a class="text-center btn btn-outline-light" href="http://localhost:3000/facturas/ver/{{$details['numero']}}"><b>VER FACTURA</b></a>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>

</html>
