<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Novo Pedido — NUTERRA | logistics</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fb; font-family:'Segoe UI', Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px; background:#f5f7fb;">
<tr>
<td align="center">
<table width="640" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 6px 18px rgba(17,24,39,0.07);">

    <!-- Header (cores do backoffice) -->
   <tr>
        <td style="background:#222e3c; color:#fff; padding:20px 24px;">
            <div style="font-weight:700; letter-spacing:.5px; font-size:14px; opacity:.9;">
                NUTERRA <span>|</span> <span style="color:#ffbb00;">logistics</span>
            </div>
        </td>
    </tr>

    <!-- Corpo -->
    <tr>
        <td style="padding:28px;">
            <h2 style="margin:0 0 6px; color:#0f172a; font-size:20px; font-weight:800; letter-spacing:.3px;">
                PEDIDOS A FORNECEDORES - Novo pedido</span>
            </h2>

            <p style="margin:8px 0 18px; color:#475569; font-size:14px;">
                Foi gerado um novo pedido através da plataforma <strong>NUTERRA <span style="opacity:.6;">|</span> logistics</strong>.
                Em anexo seguem os ficheiros com os produtos <em>organizados por fornecedor</em>.
            </p>

            <!-- Lista de anexos -->
            <div style="margin:18px 0;">
                <h3 style="margin:0 0 8px; font-size:15px; color:#0f172a;">Ficheiros anexos</h3>
                <ul style="margin:0; padding-left:18px; color:#475569; font-size:14px;">
                    <pre style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:8px; padding:12px; font-family:'Courier New', monospace; font-size:13px; color:#334155; white-space:pre-wrap;">
@foreach ($downloadLinks as $file)
📎 {{ $file }}
@endforeach
            </pre>
                </ul>
            </div>

            <p style="margin:24px 0 0; color:#64748b; font-size:14px;">
                Para qualquer questão, contacte diretamente o armazém.
            </p>

            <!-- Rodapé -->
            <div style="margin:22px 0 0; padding-top:14px; border-top:1px solid #e5e7eb;">
                <p style="margin:0; font-size:12px; color:#94a3b8;">
                    Este é um email automático gerado pela plataforma NUTERRA
                    <span style="opacity:.6;">|</span>
                    <span style="color:#f5a524;">logistics</span>.
                </p>
            </div>
        </td>
    </tr>

</table>
</td>
</tr>
</table>

</body>
</html>
