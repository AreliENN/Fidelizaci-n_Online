<?php
session_start();

// Simulación de base de datos
$usuarios = [
    'admin' => [
        ['telefono' => '1234567890', 'contraseña' => 'admin123', 'id' => 1, 'nombre' => 'Administrador Uno'],
    ],
    'cliente' => [
        ['telefono_movil' => '0987654321', 'contraseña' => 'cliente456', 'id' => 101, 'nombre' => 'Cliente Uno'],
    ]
];

function autenticar($telefono, $password) {
    global $usuarios;

    echo "🔎 Buscando usuario con teléfono: $telefono\n";

    foreach ($usuarios['admin'] as $admin) {
        if ($admin['telefono'] === $telefono) {
            echo "Usuario encontrado en tabla: administrador\n";
            return $password === $admin['contraseña']
                ? ['id' => $admin['id'], 'nombre' => $admin['nombre'], 'rol' => 'admin']
                : false;
        }
    }

    foreach ($usuarios['cliente'] as $cliente) {
        if ($cliente['telefono_movil'] === $telefono) {
            echo "Usuario encontrado en tabla: cliente\n";
            return $password === $cliente['contraseña']
                ? ['id' => $cliente['id'], 'nombre' => $cliente['nombre'], 'rol' => 'cliente']
                : false;
        }
    }

    echo "Usuario no encontrado en ninguna tabla.\n";
    return false;
}

$casos = [
    ['telefono' => '1234567890', 'password' => 'admin123',     'esperado' => 'admin'],
    ['telefono' => '0987654321', 'password' => 'cliente456',   'esperado' => 'cliente'],
    ['telefono' => '1234567890', 'password' => 'clave_invalida','esperado' => false],
    ['telefono' => '1111111111', 'password' => 'ninguna',       'esperado' => false],
];

echo "=== INICIO DE PRUEBAS DE AUTENTICACIÓN ===\n\n";

foreach ($casos as $i => $caso) {
    echo "Prueba #" . ($i + 1) . "\n";
    echo "Entrada:\n";
    echo "   Teléfono: " . $caso['telefono'] . "\n";
    echo "   Contraseña: " . $caso['password'] . "\n";

    $inicio = microtime(true);
    $resultado = autenticar($caso['telefono'], $caso['password']);
    $duracion = round((microtime(true) - $inicio) * 1000, 2);

    echo "⏱ Tiempo de ejecución: {$duracion} ms\n";

    echo "Resultado obtenido: ";
    if ($resultado === false) {
        echo "Fallo en autenticación\n";
    } else {
        echo "Autenticado como " . strtoupper($resultado['rol']) . "\n";
    }

    echo "Resultado esperado: ";
    echo $caso['esperado'] === false ? "Fallo esperado\n" : "Rol esperado: " . strtoupper($caso['esperado']) . "\n";

    if ($resultado === false && $caso['esperado'] === false) {
        echo "Resultado: OK - Fallo correctamente\n";
    } elseif (is_array($resultado) && $resultado['rol'] === $caso['esperado']) {
        echo "Resultado: OK - Autenticación correcta como " . $resultado['rol'] . "\n";
    } else {
        echo "Resultado: ERROR - No coincide con lo esperado\n";
    }

    echo "--------------------------------------\n";
}

echo "\n=== PRUEBAS FINALIZADAS ===\n";
?>
