import webpush from 'web-push';
import express from 'express';

const app = express();
app.use(express.json());

// Tus claves VAPID
const publicVapidKey = 'BPHRfg8JX82OURSSIDADQn77BSzQkf2DIFpJllgCQuSm4ra7OyV27zf5aXFQK_D-V-6xtNJxJno79fqM4OjUe9I';
const privateVapidKey = 'BHDCeQ9O2bMyshH2C-JEByoUila2YYl01_jIo5-c4o0';

webpush.setVapidDetails(
  'mailto:tucorreo@dominio.com',
  publicVapidKey,
  privateVapidKey
);

// Endpoint para guardar la suscripción del cliente
app.post('/subscribe', (req, res) => {
  const subscription = req.body;

  // Aquí podrías guardarla en tu base de datos
  console.log('Nueva suscripción:', subscription);

  // Envía una notificación de prueba
  const payload = JSON.stringify({
    title: '¡Hola!',
    body: 'Notificación enviada correctamente 🚀'
  });

  webpush.sendNotification(subscription, payload)
    .then(() => res.status(201).json({ message: 'Notificación enviada' }))
    .catch(err => {
      console.error(err);
      res.status(500).json({ error: 'Error al enviar notificación' });
    });
});

app.listen(3000, () => console.log('Servidor corriendo en puerto 3000'));
