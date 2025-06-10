<?php
ob_start(); 
session_start();

$destino = "login.php"; // Valor por defecto, por si no hay rol

if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'administrador') {
        $destino = "../ADMIN/index.php";
    } elseif ($_SESSION['rol'] === 'empleado') {
        $destino = "../EMPLEADO/index_empleado.php";
    } else {
        $destino = "login.php"; // Si el rol no es reconocido, lo mandamos al login
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Animación completa SVG</title>
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background-color: #17A595;
      opacity: 0;
      animation: fadeInPage 2s ease-out forwards; /* Animación de entrada de la página */
    }

    svg {
      width: 700px; /* Ajusta el tamaño que quieras */
      height: auto;
      display: block;
      margin-bottom: 20px; /* Espacio entre el SVG y el texto */
      opacity: 0;
      animation: fadeInSvg 1.5s ease-out forwards; /* Animación de entrada del SVG */
    }

    path {
      fill: none;
      stroke: #ffffff;
      stroke-width: 15;
      animation: dibujar 1.5s ease-out forwards;
    }

    @keyframes dibujar {
      to {
        stroke-dashoffset: 0;
      }
    }

    .name-container {
      opacity: 0;
      animation: fadeInText 2s ease-out forwards;
      animation-delay: 1.5s; /* Espera a que termine el SVG */
      text-align: center;
    }

    .logo-name {
      color: #fff;
      font-size: 24px;
      letter-spacing: 8px;
      text-transform: uppercase;
      font-weight: bolder;
    }

    /* Animación de entrada de la página */
    @keyframes fadeInPage {
      0% {
        opacity: 0.5;
        transform: scale(0.5); /* Empieza pequeño */
      }
      100% {
        opacity: 1;
        transform: scale(1); /* Termina en tamaño normal */
      }
    }

    /* Animación de entrada del SVG */
    @keyframes fadeInSvg {
      0% {
        opacity: 0.5;
        transform: scale(1.5); /* Empieza más pequeño */
      }
      100% {
        opacity: 1;
        transform: scale(1); /* Termina con el tamaño original */
      }
    }

    /* Animación de entrada del texto */
    @keyframes fadeInText {
      from {
        opacity: 0;
        transform: translateY(15px); /* Texto comienza abajo */
      }
      to {
        opacity: 1;
        transform: translateY(0); /* Texto se mueve a su posición original */
      }
    }

    /* Animación de salida de la página (similar a la entrada, pero hacia atrás) */
    @keyframes fadeOutPage {
      0% {
        opacity: 1;
        transform: scale(1); /* Empieza con el tamaño original */
      }
      100% {
        opacity: 0;
        transform: scale(0.5) translateY(50px); /* Se reduce y se mueve hacia abajo, como si se alejase */
      }
    }
  </style>
</head>

<body>


  <svg xmlns="http://www.w3.org/2000/svg" width="900pt" height="700pt" viewBox="0 0 500 250" preserveAspectRatio="xMidYMid meet">
    <g transform="translate(0.000000,500.000000) scale(0.100000,-0.100000)" fill="none" stroke="#17A595">
      <path d="M1330 4494 c-114 -39 -315 -149 -425 -233 -222 -170 -344 -325 -405
      -513 l-21 -66 23 -74 c30 -95 31 -272 3 -435 -18 -106 -18 -115 -2 -172 24
      -87 22 -159 -9 -249 l-27 -77 22 -61 c20 -56 22 -79 20 -245 -2 -206 8 -409
      22 -423 10 -12 13 32 13 259 1 88 5 203 10 255 7 79 6 106 -8 154 -17 56 -16
      59 5 119 27 76 35 197 20 276 -10 53 -9 69 10 138 30 111 39 378 15 467 -17
      61 -17 63 7 129 14 37 40 93 58 124 52 87 227 258 344 335 138 92 320 181 368
      182 l38 1 73 -167 c128 -290 149 -323 217 -359 44 -22 49 -33 49 -109 0 -31 7
      -97 15 -146 18 -102 89 -322 120 -368 20 -30 23 -31 130 -41 127 -12 115 3
      138 -158 11 -72 25 -118 51 -173 23 -47 45 -113 58 -179 11 -58 27 -113 34
      -123 10 -14 29 -17 115 -17 l103 0 12 -42 c15 -50 34 -200 34 -265 0 -37 13
      -72 66 -178 36 -72 68 -130 70 -127 3 3 -15 60 -41 127 -39 106 -46 137 -55
      243 -17 200 -28 254 -57 279 -9 7 -53 15 -105 19 -102 6 -108 10 -108 81 0 28
      -18 104 -44 185 -26 81 -50 183 -59 247 -23 161 -45 182 -187 170 l-76 -6 -47
      149 c-49 150 -67 256 -67 385 0 102 -18 138 -71 138 -48 0 -67 31 -193 310
      -54 118 -105 223 -113 233 -20 21 -81 22 -143 1z"/>
      <path d="M1404 3729 c-176 -37 -363 -112 -504 -204 -68 -44 -174 -134 -166
      -142 2 -2 57 28 121 67 193 117 298 162 535 230 80 23 156 48 170 56 24 13 23
      13 -20 13 -25 0 -86 -9 -136 -20z"/>
      <path d="M1555 3551 c-71 -42 -93 -121 -77 -271 6 -58 17 -109 24 -116 10 -10
      24 -11 57 -3 73 16 84 30 21 27 l-55 -3 -3 111 c-3 126 9 177 50 207 23 17 31
      19 53 9 34 -16 52 -62 61 -156 4 -42 11 -79 15 -82 12 -7 11 199 -1 230 -11
      29 -64 66 -95 66 -11 0 -34 -8 -50 -19z"/>
      <path d="M1255 3516 c-37 -16 -53 -48 -65 -134 -11 -83 2 -241 21 -253 20 -12
      49 -10 84 6 l30 13 -52 1 -51 1 5 103 c8 181 25 232 74 225 42 -6 59 -45 66
      -150 3 -54 10 -95 14 -92 12 7 11 207 -1 239 -6 15 -24 32 -45 40 -41 18 -42
      18 -80 1z"/>
      <path d="M2738 3251 c-20 -17 -43 -49 -52 -71 -8 -21 -25 -44 -36 -50 -12 -6
      -80 -14 -151 -17 -71 -3 -129 -10 -129 -15 0 -4 5 -8 12 -8 6 0 62 -5 122 -11
      148 -14 176 -5 230 74 l40 57 65 0 c89 0 484 -36 618 -55 294 -44 727 -155
      853 -220 67 -34 121 -77 131 -103 6 -15 21 -178 34 -362 28 -381 43 -531 56
      -545 5 -5 9 184 9 459 0 524 2 504 -73 572 -74 68 -297 158 -539 219 -276 71
      -463 92 -878 102 l-276 6 -36 -32z"/>
      <path d="M1555 3115 c-324 -35 -537 -89 -733 -189 -65 -33 -114 -65 -100 -66
      3 0 59 21 123 46 204 79 389 120 795 175 129 17 242 35 250 40 22 13 -195 9
      -335 -6z"/>
      <path d="M1852 2914 c-44 -31 -52 -63 -52 -218 0 -79 3 -151 6 -160 5 -12 21
      -16 70 -16 76 0 85 15 14 24 l-50 7 0 117 c0 64 5 134 10 154 18 63 78 88 115
      48 14 -15 20 -46 27 -127 4 -60 11 -119 15 -133 6 -23 7 -21 14 15 11 57 11
      184 -1 224 -16 56 -46 81 -100 81 -25 0 -55 -7 -68 -16z"/>
      <path d="M1541 2907 c-65 -32 -97 -184 -71 -335 13 -81 11 -80 113 -66 62 9
      50 24 -18 24 l-54 0 -6 55 c-10 77 2 176 28 226 30 59 49 69 84 43 36 -27 52
      -73 52 -151 1 -63 14 -90 25 -50 10 40 6 171 -7 197 -13 25 -81 70 -107 70 -8
      0 -26 -6 -39 -13z"/>
      <path d="M1460 2439 c-303 -20 -775 -96 -795 -129 -2 -3 17 -3 43 1 304 46
      697 81 1072 94 280 10 515 26 524 36 12 11 -665 10 -844 -2z"/>
      <path d="M2228 2234 c-54 -29 -63 -63 -61 -232 1 -85 5 -162 8 -172 8 -27 30
      -31 112 -24 73 7 102 24 39 24 -18 0 -55 3 -81 7 l-48 6 6 131 c6 147 23 206
      63 225 52 23 105 5 128 -43 9 -19 18 -79 22 -142 8 -134 22 -169 26 -64 4 113
      -2 212 -13 241 -5 14 -23 33 -38 42 -36 21 -126 22 -163 1z"/>
      <path d="M1864 2226 c-55 -24 -69 -71 -67 -226 1 -74 6 -147 11 -161 10 -25
      11 -25 79 -18 37 5 77 9 88 10 34 3 -35 17 -89 18 l-49 1 7 142 c8 164 15 183
      76 193 79 13 109 -38 111 -193 1 -37 4 -78 8 -92 7 -24 7 -24 14 9 11 51 8
      211 -4 254 -8 31 -18 42 -52 58 -49 22 -90 23 -133 5z"/>
      <path d="M1523 2205 c-51 -36 -77 -196 -53 -333 11 -66 14 -72 33 -68 12 3 44
      8 71 11 59 8 45 25 -21 25 -40 0 -42 1 -49 36 -8 42 2 172 16 225 13 47 41 71
      76 67 40 -5 63 -43 70 -118 7 -88 13 -112 24 -105 15 10 12 171 -4 210 -24 57
      -113 85 -163 50z"/>
    </g>
  </svg>

  <div class="name-container">
    <div class="logo-name">Multiservicios Roma</div>
  </div>

<script>
  // Seleccionamos todos los paths dentro del SVG
  const paths = document.querySelectorAll('svg path');

  paths.forEach(path => {
    const length = path.getTotalLength();
    // Asignamos el stroke-dasharray y stroke-dashoffset igual a la longitud
    path.style.strokeDasharray = length;
    path.style.strokeDashoffset = length;
  });

  setTimeout(function() {
    document.body.style.animation = "fadeOutPage 2s ease-out forwards"; // Activamos la animación de salida
    setTimeout(function() {
    }, 2000); // Tiempo de duración de la animación de salida (2 segundos)
  }, 3000);
</script>
<svg ></svg>
</body>
</html>

<?php
header("refresh:4.5; url=$destino");// Redirigir a la página de destino después de 3 segundos // O la página a la que quieras redirigir después de la animación
exit();
ob_end_flush(); 
?>
