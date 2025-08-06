<?php
require 'Verificacion_calculo_puntos.php';

function testCalculoPuntos() {
    $casos = [
        ['monto' => 99, 'esperado' => 0],
        ['monto' => 100, 'esperado' => 5],
        ['monto' => 250, 'esperado' => 10],
        ['monto' => 399.99, 'esperado' => 15],
        ['monto' => 0, 'esperado' => 0],
        ['monto' => 1000, 'esperado' => 50], 
    ];

    foreach ($casos as $caso) {
        $resultado = calcularPuntos($caso['monto']);
        $estado = ($resultado == $caso['esperado']) ? '✅ OK' : '❌ ERROR';
        echo "Monto: {$caso['monto']} - Esperado: {$caso['esperado']} - Obtenido: {$resultado} => $estado\n";
    }
}

testCalculoPuntos();
?>
