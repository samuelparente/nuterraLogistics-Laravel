@php
    $suppliers = $orderSummary['suppliers'] ?? [];
    $count     = (int)($orderSummary['count'] ?? count($suppliers));
@endphp

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
<table width="800" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 6px 18px rgba(17,24,39,0.07);">

    <!-- Header -->
    <tr>
        <td style="background:#222e3c; color:#fff; padding:20px 24px;">
            <div style="font-weight:700; letter-spacing:.5px; font-size:14px;">
                NUTERRA | <span style="color:#ffbb00;">logistics</span>
            </div>
        </td>
    </tr>

    <!-- Corpo -->
    <tr>
        <td style="padding:28px; background:#ffffff; color:#000000; font-size:14px;">
            <h2 style="margin:0 0 12px; font-size:20px; font-weight:800; letter-spacing:.3px; color:#000000;">
                PEDIDOS A FORNECEDORES - Novo pedido
            </h2>

            <p style="margin:0 0 16px; color:#475569; font-size:14px;">
                Foi gerado um novo pedido através da plataforma
                <strong>NUTERRA | logistics</strong>.
                Os ficheiros seguem em anexo (organizados por fornecedor).
            </p>

            @if($count <= 0)
                <p style="margin:12px 0; color:#b45309; font-size:14px;">
                    Não foram encontrados dados de resumo para este pedido.
                </p>
            @else
                <div style="margin:18px 0;">
                    <h3 style="margin:0 0 8px; font-size:15px; color:#0f172a;">Resumo do pedido</h3>

                    <div style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th align="left"  style="border-bottom:1px solid #e5e7eb; padding:10px 8px; font-size:12px; color:#64748b;">Fornecedor</th>
                                    <th align="left"  style="border-bottom:1px solid #e5e7eb; padding:10px 8px; font-size:12px; color:#64748b;">Marca(s)</th>
                                    <th align="right" style="border-bottom:1px solid #e5e7eb; padding:10px 8px; font-size:12px; color:#64748b;">Produtos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $s)
                                    @php
                                        $brandsList = !empty($s['brands']) ? implode(', ', $s['brands']) : '—';
                                    @endphp
                                    <tr>
                                        <td style="padding:10px 8px; font-size:13px; color:#0f172a;">{{ $s['supplier'] }}</td>
                                        <td style="padding:10px 8px; font-size:13px; color:#0f172a;">{{ $brandsList }}</td>
                                        <td align="right" style="padding:10px 8px; font-size:13px; color:#0f172a;">
                                            {{ $s['lines'] ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <p style="margin:24px 0 0; color:#64748b; font-size:14px;">
                Para qualquer questão, contacte diretamente o armazém.
            </p>

            <!-- Rodapé -->
            <div style="margin:22px 0 0; padding-top:14px; border-top:1px solid #e5e7eb;">
                <p style="margin:0; font-size:12px; color:#94a3b8;">
                    Este é um email automático gerado pela plataforma NUTERRA
                    <span style="opacity:.6;">|</span>
                    <span style="color:#ffbb00;">logistics</span>.
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
