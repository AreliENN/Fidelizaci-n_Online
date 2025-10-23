'use strict';

document.addEventListener("DOMContentLoaded", () => {
  const btnNotificar = document.getElementById("btnNotificar");

  if (!btnNotificar) {
    console.error("❌ No se encontró el botón #btnNotificar en el HTML");
    return;
  }

  btnNotificar.addEventListener("click", async () => {
    if (!("Notification" in window)) {
      console.error("Este navegador no soporta notificaciones.");
      return;
    }

    const permiso = Notification.permission;
    console.log("Estado actual del permiso:", permiso);

    if (permiso === "granted") {
      console.log("✅ Permiso concedido, mostrando notificación...");
      mostrarNotificacion();
    } else if (permiso === "default") {
      const nuevoPermiso = await Notification.requestPermission();
      console.log("Nuevo permiso:", nuevoPermiso);
      if (nuevoPermiso === "granted") mostrarNotificacion();
    } else {
      console.warn("El usuario no aceptó recibir notificaciones");
    }
  });

  function mostrarNotificacion() {
    const opciones = {
      body: "Este es un texto de prueba para la notificación.",
      icon: "icons/icon-96x96.png"
    };
    const notificacion = new Notification("Mi notificación", opciones);
    console.log("📢 Notificación mostrada:", notificacion);
    setTimeout(() => notificacion.close(), 5000);
  }
});
