<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan Insiden Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
  <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #dc2626;">🚨 Laporan Insiden Baru</h2>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
      <h3 style="margin-top: 0;">Detail Tiket:</h3>
      <p><strong>ID Tiket:</strong> {{ $incident->case_id }}</p>
      <p><strong>Prioritas:</strong> {{ $incident->priority }}</p>
      <p><strong>Kategori:</strong> {{ $incident->incidentType->name }}</p>
      <p style="margin-bottom: 0;"><strong>Waktu Kejadian:</strong> {{ \Carbon\Carbon::parse($incident->incident_at)->format('d/m/Y H:i') }}</p>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
      <h3 style="margin-top: 0;">Informasi Pelapor:</h3>
      <p><strong>Nama:</strong> {{ $incident->reporter_name }}</p>
      <p><strong>Email:</strong> {{ $incident->reporter_email }}</p>
      <p style="margin-bottom: 0;"><strong>Telepon:</strong> {{ $incident->reporter_phone ?? 'Tidak tersedia' }}</p>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
      <h3 style="margin-top: 0;">Deskripsi Insiden:</h3>
      <p style="margin-bottom: 0; white-space: pre-wrap;">{{ $incident->description }}</p>
    </div>

    <p style="color: #aaa; margin-top: 20px;">
      Email ini dikirim secara otomatis oleh sistem CSIRT Bojonegoro.
    </p>
  </div>
</body>
</html>
