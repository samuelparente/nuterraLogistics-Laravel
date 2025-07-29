@component('mail::message')
# Novo Pedido de Produtos

Olá,

Foi gerado um novo pedido de produtos a fornecedores através da plataforma **NUTERRA | logistics**.

Os ficheiros em anexo contêm os produtos organizados por fornecedor:

@foreach ($downloadLinks as $file)
- {{ $file }}
@endforeach

Caso exista alguma dúvida, contactar diretamente o armazém.

---

<span style="font-size: 13px; color: #999;">Este é um email automático gerado pela plataforma Nuterra | Logistics.</span>
@endcomponent
