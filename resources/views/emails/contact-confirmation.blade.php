<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Pesan - CSIRT Bojonegoro</title>
  <style>
    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
    .header { background: linear-gradient(135deg, #1e293b, #3730a3); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
    .content { background: #f8fafc; padding: 30px; border-radius: 0 0 8px 8px; }
    .success-box { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 20px; border-radius: 8px; margin: 20px 0; }
    .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981; }
    .label { font-weight: bold; color: #475569; }
    .value { margin-bottom: 15px; }
    .footer { background: #1e293b; color: #94a3b8; padding: 20px; text-align: center; font-size: 12px; }
    .cta-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 20px; border-radius: 8px; margin: 20px 0; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
        <h1 style="margin: 0;">🛡️ CSIRT Bojonegoro</h1>
        <p style="margin: 10px 0 0 0;">Konfirmasi Pesan Anda</p>
    </div>

    <div class="content">
      <div class="success-box">
        <h3 style="color: #059669; margin-top: 0;">✅ Pesan Berhasil Diterima!</h3>
        <p style="margin-bottom: 0; color: #065f46;">
          Terima kasih, <strong>{{ $contactData['name'] }}</strong>. Pesan Anda telah berhasil diterima oleh tim CSIRT Bojonegoro.
        </p>
      </div>

      <div class="info-box">
        <div class="value">
          <span class="label">Subjek:</span><br>
          {{ $contactData['subject'] }}
        </div>

        <div class="value">
          <span class="label">Jenis Pesan:</span><br>
          {{ $typeLabel }}
        </div>

        <div class="value">
          <span class="label">Waktu Dikirim:</span><br>
          {{ now()->format('d F Y, H:i') }} WIB
        </div>
      </div>

      <div class="cta-box">
        <h4 style="color: #1d4ed8; margin-top: 0;">📧 Apa Selanjutnya?</h4>
        <ul style="color: #1e40af; margin-bottom: 0;">
          <li>Tim CSIRT akan meninjau pesan Anda dalam 1x24 jam</li>
          <li>Anda akan menerima balasan melalui email ini</li>
          <li>Untuk keperluan mendesak, hubungi hotline kami di <strong>0353-881826</strong></li>
        </ul>
      </div>

      @if($contactData['type'] === 'report')
      <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <strong style="color: #dc2626;">⚠️ Laporan Anda Penting</strong><br>
        <span style="color: #991b1b;">Karena Anda melaporkan insiden, tim kami akan memberikan prioritas khusus untuk menindaklanjuti pesan Anda.</span>
      </div>
      @endif

      <div style="text-align: center; margin: 30px 0;">
        <p style="margin-bottom: 0; color: #64748b;">
          Butuh bantuan segera? Hubungi hotline darurat kami
        </p>
        <h3 style="color: #dc2626; margin: 10px 0;">📞 0353-881826</h3>
        <p style="margin-top: 0; color: #64748b; font-size: 14px;">
          Tersedia 24/7 untuk laporan insiden keamanan siber
        </p>
      </div>
    </div>

    <div class="footer">
      <p><strong>CSIRT Bojonegoro</strong></p>
      <p>Dinas Komunikasi dan Informatika</p>
      <p>Jl. P. Mas Tumapel No. 1, Bojonegoro, Jawa Timur 62115</p>
      <p>Email: ttis@bojonegorokab.go.id | Telp: (0353) 881826</p>
    </div>
  </div>
</body>
</html>
