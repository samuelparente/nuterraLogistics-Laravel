<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Novo Pedido - Nuterra Logistics</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family: 'Segoe UI', sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td align="center" style="background-color: #1d1f2f; padding: 20px;">
                            <img src="cid:nuterra-logo" alt="NUTERRA Logistics" style="max-width: 180px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #000000;">Novo Pedido de Produtos</h2>

                            <p style="color:#444444; font-size:15px;">
                                Olá,<br><br>
                                Foi gerado um novo pedido de produtos a fornecedores através da plataforma <strong>NUTERRA | logistics</strong>.
                            </p>

                            <p style="color:#444444; font-size:15px; margin-top: 20px;">
                                Os ficheiros em anexo contêm os produtos organizados por fornecedor:
                            </p>

                            <ul style="color:#444444; font-size:15px;">
                                @foreach ($downloadLinks as $file)
                                    <li>📎 {{ $file }}</li>
                                @endforeach
                            </ul>

                            <p style="color:#444444; font-size:14px; margin-top: 30px;">
                                Caso exista alguma dúvida, contactar diretamente o armazém.
                            </p>

                            <p style="color:#999999; font-size:12px; border-top:1px solid #eee; padding-top:20px; margin-top:30px;">
                                Este é um email automático gerado pela plataforma Nuterra | Logistics.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
