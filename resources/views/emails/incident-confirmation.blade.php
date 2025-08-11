<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Konfirmasi Tiket</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
  <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #2563eb;">✅ Tiket Berhasil Dibuat</h2>

    <p>Yth. {{ $incident->reporter_name }},</p>

    <p>Tiket laporan insiden Anda telah berhasil dibuat dan sedang diproses oleh tim CSIRT Bojonegoro.</p>

    <div style="background: #dbeafe; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563eb;">
      <h3 style="margin-top: 0;">Detail Tiket Anda:</h3>
      <p><strong>ID Tiket:</strong> {{ $incident->case_id }}</p>
      <p><strong>Status:</strong> {{ $incident->status }}</p>
      <p><strong>Prioritas:</strong> {{ $incident->priority }}</p>
      <p style="margin-bottom: 0;"><strong>Dilaporkan:</strong> {{ \Carbon\Carbon::parse($incident->reported_at)->format('d/m/Y H:i') }}</p>
    </div>

    <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #818181;">
      <h3 style="margin-top: 0;">Langkah Selanjutnya:</h3>
      <ul style="margin-bottom: 0;">
        <li style="margin-left: 0;">Tim kami akan meninjau laporan Anda dalam 1x24 jam</li>
        <li style="margin-left: 0;">Anda akan dihubungi jika diperlukan informasi tambahan</li>
        <li style="margin-left: 0;">Simpan ID Tiket untuk melacak perkembangan</li>
        <li style="margin-left: 0;">Cek tiket secara berkala di website CSIRT Bojonegoro</li>
      </ul>
    </div>

    <div style="background: #fef2f2; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc2626;">
      <h3 style="margin-top: 0;"><strong>Untuk insiden darurat:</strong></h3>
      <p style="margin: 5px 0 0 0;">Hubungi hotline 24/7: <strong>0353-881826</strong></p>
    </div>

    <p>Terima kasih atas kepercayaan Anda kepada CSIRT Bojonegoro.</p>

    <p style="color: #aaa; margin-top: 30px;">
      Email ini dikirim secara otomatis. Jangan membalas email ini.
    </p>
  </div>
</body>
</html>
