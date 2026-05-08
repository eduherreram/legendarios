<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial, Helvetica, sans-serif;color:#0f172a;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
            <div style="padding:20px 20px 0 20px;">
                <h1 style="margin:0;font-size:20px;line-height:1.3;">{{ $reconocimiento->nombre_reconocimiento }}</h1>
                <div style="margin-top:6px;font-size:13px;color:#64748b;">
                    Para {{ trim(($reconocimiento->nombre ?? '').' '.($reconocimiento->apellido ?? '')) ?: 'ti' }}
                </div>
            </div>

            @if($reconocimiento->imagen_path)
                <div style="padding:16px 20px 0 20px;">
                    <img src="{{ asset('storage/'.$reconocimiento->imagen_path) }}" alt="" style="display:block;width:100%;height:auto;border-radius:12px;" />
                </div>
            @endif

            <div style="padding:16px 20px 20px 20px;font-size:15px;line-height:1.7;color:#334155;white-space:pre-line;">
                {{ $reconocimiento->descripcion }}
            </div>
        </div>

        <div style="padding:14px 6px 0 6px;font-size:12px;color:#64748b;">
            Enviado desde Legendario Manager.
        </div>
    </div>
</body>
</html>
