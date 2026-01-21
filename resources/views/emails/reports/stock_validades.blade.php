@php
    $totalLinhas = (int) ($totalLinhas ?? 0);
    $monthsAhead = isset($monthsAhead) ? $monthsAhead : null; // pode ser null
    $redDays     = (int) ($redDays ?? 60);
    $yellowDays  = (int) ($yellowDays ?? 60);

    $periodText = $monthsAhead
        ? "validades dentro dos próximos {$monthsAhead} meses"
        : "validades dentro do período configurado no relatório";

    $hasData = $totalLinhas > 0;
@endphp

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório diário de Validades — NUTERRA | logistics</title>
</head>

<body style="margin:0; padding:0; background:#f5f7fb; font-family:'Segoe UI', Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px; background:#f5f7fb;">
<tr>
<td align="center">

<table width="800" cellpadding="0" cellspacing="0"
       style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 6px 18px rgba(17,24,39,0.07);">

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

            <h2 style="margin:0 0 10px; font-size:18px; font-weight:800; letter-spacing:.3px; color:#0f172a;">
                RELATÓRIO DIÁRIO DE VALIDADES 
            </h2>

            <p style="margin:0 0 18px; color:#475569; font-size:14px;">
                Segue em anexo o relatório dos lotes a terminar em 6 meses a partir da data deste relatório.
            </p>

            <!-- Cards principais -->
            <div style="margin:18px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:separate; border-spacing:0 12px;">
                    <tr>
                        <!-- Card 1 -->
                        <td style="width:50%; padding-right:8px;">
                            <div style="border:1px solid #e5e7eb; border-radius:12px; padding:14px 14px; background:#ffffff;">
                                <div style="font-size:12px; color:#64748b; margin-bottom:6px;">Produtos no relatório</div>
                                <div style="font-size:20px; font-weight:800; color:#0f172a;">
                                    {{ $totalLinhas }}
                                </div>
                                <div style="font-size:12px; color:#94a3b8; margin-top:6px;">
                                </div>
                            </div>
                        </td>

                        <!-- Card 2 -->
                        <td style="width:50%; padding-left:8px;">
                            <div style="border:1px solid #e5e7eb; border-radius:12px; padding:14px 14px; background:#ffffff;">
                                <div style="font-size:12px; color:#64748b; margin-bottom:6px;">Legenda de cores</div>

                                <div style="line-height:0;">
                                    <span style="display:inline-block; margin:6px 8px 0 0; padding:4px 10px; border-radius:999px;
                                                 background:#ffe4e6; border:1px solid #fecdd3; color:#9f1239; font-size:13px; line-height:1.4;">
                                        Vermelho ≤ {{ $redDays }} dias
                                    </span>

                                    <span style="display:inline-block; margin:6px 8px 0 0; padding:4px 10px; border-radius:999px;
                                                 background:#fff8db; border:1px solid #ffd24d; color:#7a4d00; font-size:13px; line-height:1.4;">
                                        Amarelo &gt; {{ $yellowDays }} dias
                                    </span>
                                </div>

                                <div style="font-size:12px; color:#94a3b8; margin-top:10px;">
                                    Baseado na diferença entre a validade e a data de hoje.
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Card aviso -->
            <div style="margin:18px 0 0;">
                @if(!$hasData)
                    <div style="border:1px solid #fed7aa; border-radius:12px; padding:14px; background:#fff7ed;">
                        <div style="font-weight:700; color:#9a3412; margin-bottom:6px;">
                            Sem resultados
                        </div>
                        <div style="color:#7c2d12; font-size:13px; line-height:1.5;">
                            Não foram encontrados lotes com stock &gt; 0 dentro do período configurado.
                        </div>
                    </div>
                @else
                    
                @endif
            </div>

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
