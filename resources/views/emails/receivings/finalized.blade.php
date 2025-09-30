@php
    $supplier  = $summary['supplier_name'] ?? 'Fornecedor';
    $brands    = $summary['brands'] ?? [];           // marcas concluídas nesta ação
    $meta      = $summary['meta'] ?? [];
    $brandPart = isset($brandPart)
        ? $brandPart
        : (count($brands) === 1 ? ($brands[0] ?? 'Marca') : (count($brands).' marcas'));
@endphp

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Receção Concluída — NUTERRA | logistics</title>
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
            <h2 style="margin:0 0 8px; font-size:16px; font-weight:600; letter-spacing:.3px; color:#000000;">
                RECEBIDO: <span style="color:#ffbb00;">{{ $brandPart }}</span>
                <span style="color:#94a3b8;"> | </span> {{ $supplier }}
            </h2>

            <p style="margin:8px 0 20px; color:#475569; font-size:14px;">
                Esta mensagem refere-se <strong>apenas</strong> às marca(s) listadas abaixo.
                O fornecedor pode ter outras marcas ainda <em>pendentes</em> de receção.
            </p>

            {{-- Marcas concluídas (chips com espaçamento) --}}
            @if(!empty($brands))
                <div style="margin:0 0 22px;">
                    <h3 style="margin:0 0 10px; font-size:15px; color:#0f172a;">Marcas concluídas</h3>
                    <div style="line-height:0;">
                        @foreach($brands as $b)
                            <span style="display:inline-block; margin:6px 8px 0 0; padding:4px 10px; border-radius:999px; background:#fff8db; border:1px solid #ffd24d; color:#7a4d00; font-size:13px; line-height:1.4;">
                                {{ $b }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Divergências --}}
            <div style="margin:24px 0;">
                <h3 style="margin:0 0 10px; font-size:15px; color:#0f172a;">Divergências face ao encomendado</h3>

                @if(empty($divergences))
                    <p style="margin:0; color:#16a34a; font-size:14px;">Sem divergências.</p>
                @else
                    <div style="border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th align="left"  style="border-bottom:1px solid #e5e7eb; padding:12px 10px; font-size:12px; color:#64748b;">SKU</th>
                                    <th align="left"  style="border-bottom:1px solid #e5e7eb; padding:12px 10px; font-size:12px; color:#64748b;">Produto</th>
                                    <th align="right" style="border-bottom:1px solid #e5e7eb; padding:12px 10px; font-size:12px; color:#64748b;">Encomendado</th>
                                    <th align="right" style="border-bottom:1px solid #e5e7eb; padding:12px 10px; font-size:12px; color:#64748b;">Recebido</th>
                                    <th align="right" style="border-bottom:1px solid #e5e7eb; padding:12px 10px; font-size:12px; color:#64748b;">Dif.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($divergences as $row)
                                    <tr>
                                        <td style="padding:12px 10px; font-size:13px; color:#0f172a;">{{ $row['sku'] }}</td>
                                        <td style="padding:12px 10px; font-size:13px; color:#0f172a;">{{ $row['name'] }}</td>
                                        <td align="right" style="padding:12px 10px; font-size:13px; color:#0f172a;">{{ $row['ordered_qty'] }}</td>
                                        <td align="right" style="padding:12px 10px; font-size:13px; color:#0f172a;">{{ $row['received_qty'] }}</td>
                                        <td align="right" style="padding:12px 10px; font-size:13px; color:#0f172a;">
                                            <span style="display:inline-block; padding:2px 10px; border-radius:999px; background:#fff8db; border:1px solid #ffd24d; color:#7a4d00;">
                                                {{ $row['diff'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Novos itens criados (is_new = 1) --}}
            <div style="margin:26px 0 0;">
                <h3 style="margin:0 0 10px; font-size:15px; color:#0f172a;">Produtos novos criados na receção</h3>
                @if(empty($newItems))
                    <p style="margin:0; color:#64748b; font-size:14px;">Não foram registados novos produtos.</p>
                @else
                    <ul style="margin:0; padding-left:18px; color:#475569; font-size:14px;">
                        @foreach($newItems as $n)
                            <li style="margin:6px 0;">
                                <strong style="color:#0f172a;">{{ $n['sku'] }}</strong> — {{ $n['name'] }}
                                @if(!empty($n['received_qty'])) ({{ $n['received_qty'] }} un.) @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Meta / rodapé --}}
            <div style="margin:26px 0 0; padding-top:14px; border-top:1px solid #e5e7eb;">
                <p style="margin:0; font-size:12px; color:#94a3b8;">
                    Receção #{{ $meta['receiving_id'] ?? '-' }} <span style="opacity:.6;">|</span>
                    Pedido #{{ $meta['order_id'] ?? '-' }}<br>
                    Finalizada em {{ $meta['received_at'] ?? '-' }} por {{ $meta['received_by_name'] ?? 'Utilizador' }}
                </p>
                @if(!empty($meta['notes']))
                    <p style="margin:10px 0 0; font-size:12px; color:#64748b;">
                        <em>Notas: {{ $meta['notes'] }}</em>
                    </p>
                @endif
                <p style="margin:16px 0 0; font-size:12px; color:#94a3b8;">
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
