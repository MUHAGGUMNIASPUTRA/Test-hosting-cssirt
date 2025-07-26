{{-- filepath: /Users/alrezza/Documents/Work/Kominfo/Projects/csirt/resources/views/emails/contact-form.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesan Baru - CSIRT Bojonegoro</title>
  <style>
    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
    .header { background: linear-gradient(135deg, #1e293b, #3730a3); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
    .content { background: #f8fafc; padding: 30px; border-radius: 0 0 8px 8px; }
    .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3730a3; }
    .message-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
    .label { font-weight: bold; color: #475569; }
    .value { margin-bottom: 15px; }
    .footer { background: #1e293b; color: #94a3b8; padding: 20px; text-align: center; font-size: 12px; }
    .urgent { border-left-color: #dc2626; }
    .urgent .label { color: #dc2626; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1 style="margin: 0;">🛡️ CSIRT Bojonegoro</h1>
      <p style="margin: 10px 0 0 0;">Pesan Baru dari Website</p>
    </div>

    <div class="content">
      <div class="info-box {{ $contactData['type'] === 'report' ? 'urgent' : '' }}">
        <div class="value">
          <span class="label">Dari:</span><br>
          {{ $contactData['name'] }}
        </div>

        <div class="value">
          <span class="label">Email:</span><br>
          <a href="mailto:{{ $contactData['email'] }}">{{ $contactData['email'] }}</a>
        </div>

        <div class="value">
          <span class="label">Jenis Pesan:</span><br>
          {{ $typeLabel }}
        </div>

        <div class="value">
          <span class="label">Subjek:</span><br>
          {{ $contactData['subject'] }}
        </div>

        <div class="value">
          <span class="label">Waktu:</span><br>
          {{ now()->format('d F Y, H:i') }} WIB
        </div>
      </div>

      <div class="message-box">
        <div class="label">Pesan:</div>
        <div style="margin-top: 10px; white-space: pre-wrap;">{{ $contactData['message'] }}</div>
      </div>

      @if($contactData['type'] === 'report')
      <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <strong style="color: #dc2626;">⚠️ Laporan Non-Darurat</strong><br>
        <span style="color: #991b1b;">Pesan ini adalah laporan insiden. Mohon segera ditindaklanjuti.</span>
      </div>
      @endif

      <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <strong style="color: #1d4ed8;">💡 Tindak Lanjut:</strong><br>
        <span style="color: #1e40af;">Silakan balas email ini atau hubungi pengirim langsung untuk memberikan respons.</span>
      </div>
    </div>

    <div class="footer">
      <p>Email ini dikirim otomatis dari sistem CSIRT Bojonegoro</p>
      <p>Untuk laporan darurat, hubungi: 0353-881234 (24/7)</p>
    </div>
  </div>
</body>
</html>
