import { useState, useEffect } from 'react';
import {
  ShoppingBag,
  Check,
  X,
  FileText,
  Eye,
  Shirt,
  Wallet
} from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function GerenciarPedidos() {
  const [pedidos, setPedidos] = useState([]);
  const [carregando, setCarregando] = useState(true);

  // Estado do Modal getOne
  const [pedidoDetalhe, setPedidoDetalhe] = useState(null);
  const [carregandoDetalhe, setCarregandoDetalhe] = useState(false);

  const carregarPedidos = async () => {
    try {
      const resposta = await fetch(`${API_URL}?acao=gerenciar_pedidos`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      setPedidos(Array.isArray(dados) ? dados : []);
    } catch (error) {
      console.error('Erro ao carregar pedidos:', error);
    } finally {
      setCarregando(false);
    }
  };

  useEffect(() => {
    carregarPedidos();
  }, []);

  const abrirDetalhes = async (pedidoId) => {
    setCarregandoDetalhe(true);
    setPedidoDetalhe({ id: pedidoId, itens: [] });
    try {
      const resposta = await fetch(`${API_URL}?acao=detalhar_pedido&id=${pedidoId}`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.id) {
        setPedidoDetalhe(dados);
      } else {
        alert(dados.mensagem || 'Erro ao carregar detalhes do pedido.');
        setPedidoDetalhe(null);
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
      setPedidoDetalhe(null);
    } finally {
      setCarregandoDetalhe(false);
    }
  };

  const handleAprovar = async (id, pagoComSaldo) => {
    const msg = pagoComSaldo
      ? 'Aprovar este pedido pago com saldo de crédito do cliente?'
      : 'Confirmar pagamento PIX deste pedido?';
    if (!window.confirm(msg)) return;

    try {
      const resposta = await fetch(`${API_URL}?acao=aprovar_pedido&id=${id}`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        carregarPedidos();
      } else {
        alert(dados.mensagem || 'Erro ao aprovar pedido.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    }
  };

  const handleCancelar = async (id) => {
    if (!window.confirm('Cancelar pedido e devolver peças para a vitrine?')) return;
    try {
      const resposta = await fetch(`${API_URL}?acao=cancelar_pedido&id=${id}`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        carregarPedidos();
      } else {
        alert(dados.mensagem || 'Erro ao cancelar pedido.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    }
  };

  const formatarData = (dataStr) => {
    if (!dataStr) return '-';
    return new Date(dataStr).toLocaleString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const formatarMoeda = (v) =>
    Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

  const badgeStatus = (status) => {
    if (status === 'Aprovado') return 'bg-emerald-100 text-emerald-800 border-emerald-200';
    if (status === 'Cancelado') return 'bg-red-100 text-red-800 border-red-200';
    return 'bg-amber-100 text-amber-800 border-amber-200';
  };

  return (
    <div className="space-y-6 pb-8">
      <div className="bg-white p-6 rounded-xl border border-stone-200 shadow-sm">
        <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
          <ShoppingBag className="text-emerald-600" size={26} />
          <span>Gestão de Vendas & Reservas</span>
        </h2>
        <p className="text-stone-500 text-sm mt-1">
          Confira os pedidos realizados pelos clientes, visualize as peças reservadas, analise os comprovantes PIX e aprove as baixas.
        </p>
      </div>

      <div className="bg-white border border-stone-200 rounded-xl shadow-sm overflow-hidden">
        {carregando ? (
          <div className="flex justify-center items-center py-16">
            <div className="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-stone-100 border-b border-stone-200 text-xs uppercase text-stone-600 font-bold">
                  <th className="p-4">#Pedido</th>
                  <th className="p-4">Cliente</th>
                  <th className="p-4">Data</th>
                  <th className="p-4">Valor</th>
                  <th className="p-4">Comprovante</th>
                  <th className="p-4">Status</th>
                  <th className="p-4 text-center">Ações</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {pedidos.length === 0 ? (
                  <tr>
                    <td colSpan={7} className="p-8 text-center text-stone-500">
                      Nenhum pedido registrado no momento.
                    </td>
                  </tr>
                ) : (
                  pedidos.map((p) => {
                    const pagoComSaldoIntegral = Number(p.valor_total) === 0;

                    return (
                      <tr key={p.id} className="hover:bg-stone-50 transition-colors">
                        <td className="p-4 font-bold text-stone-800">#PED-{p.id}</td>
                        <td className="p-4 font-semibold text-stone-700">{p.cliente_nome}</td>
                        <td className="p-4 text-stone-600">{formatarData(p.criado_em)}</td>
                        <td className="p-4 font-bold text-stone-800">
                          {pagoComSaldoIntegral ? (
                            <span className="inline-flex items-center gap-1 text-teal-700 bg-teal-50 border border-teal-200 px-2.5 py-0.5 rounded-full text-xs">
                              <Wallet size={13} /> R$ 0,00 (Saldo)
                            </span>
                          ) : (
                            formatarMoeda(p.valor_total)
                          )}
                        </td>
                        <td className="p-4">
                          {pagoComSaldoIntegral ? (
                            <span className="text-xs text-stone-400 italic">Pago com Saldo</span>
                          ) : p.comprovante ? (
                            <a
                              href={`${UPLOADS_URL}${p.comprovante}`}
                              target="_blank"
                              rel="noreferrer"
                              className="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-800 font-bold underline text-xs"
                            >
                              <FileText size={15} />
                              <span>Anexo</span>
                            </a>
                          ) : (
                            <span className="text-xs text-stone-400">Sem anexo</span>
                          )}
                        </td>
                        <td className="p-4">
                          <span
                            className={`px-2.5 py-1 rounded-full text-xs font-bold border ${badgeStatus(
                              p.status
                            )}`}
                          >
                            {pagoComSaldoIntegral && p.status === 'Aguardando PIX'
                              ? 'Aguardando Aprovação'
                              : p.status}
                          </span>
                        </td>
                        <td className="p-4">
                          <div className="flex items-center justify-center gap-2">
                            <button
                              type="button"
                              onClick={() => abrirDetalhes(p.id)}
                              title="Ver peças do pedido"
                              className="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition"
                            >
                              <Eye size={14} />
                              <span>Peças</span>
                            </button>

                            {p.status === 'Aguardando PIX' && (
                              <>
                                <button
                                  type="button"
                                  onClick={() => handleAprovar(p.id, pagoComSaldoIntegral)}
                                  className="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition"
                                >
                                  <Check size={14} />
                                  <span>Aprovar</span>
                                </button>
                                <button
                                  type="button"
                                  onClick={() => handleCancelar(p.id)}
                                  className="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition"
                                >
                                  <X size={14} />
                                  <span>Cancelar</span>
                                </button>
                              </>
                            )}
                          </div>
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {pedidoDetalhe && (
        <div
          className="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
          onClick={() => setPedidoDetalhe(null)}
        >
          <div
            className="bg-white rounded-2xl border border-stone-200 shadow-xl max-w-2xl w-full overflow-hidden"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="bg-stone-800 text-white px-6 py-4 flex items-center justify-between">
              <div>
                <h3 className="text-lg font-black">Peças do Pedido #PED-{pedidoDetalhe.id}</h3>
                {pedidoDetalhe.cliente_nome && (
                  <p className="text-xs text-stone-300">
                    Cliente: {pedidoDetalhe.cliente_nome}
                    {pedidoDetalhe.cliente_telefone ? ` • WhatsApp: ${pedidoDetalhe.cliente_telefone}` : ''}
                  </p>
                )}
              </div>
              <button
                type="button"
                onClick={() => setPedidoDetalhe(null)}
                className="p-1.5 rounded-lg text-stone-300 hover:text-white hover:bg-stone-700 transition"
              >
                <X size={20} />
              </button>
            </div>

            <div className="p-6 max-h-[70vh] overflow-y-auto space-y-3">
              {carregandoDetalhe ? (
                <div className="flex justify-center py-10">
                  <div className="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                </div>
              ) : (pedidoDetalhe.itens || []).length === 0 ? (
                <p className="text-center text-stone-500 py-6">Nenhuma peça encontrada neste pedido.</p>
              ) : (
                pedidoDetalhe.itens.map((item) => (
                  <div
                    key={item.peca_id}
                    className="flex items-center justify-between gap-4 p-3.5 bg-stone-50 border border-stone-200 rounded-xl"
                  >
                    <div className="flex items-center gap-3.5">
                      {item.foto_caminho ? (
                        <img
                          src={`${UPLOADS_URL}${item.foto_caminho}`}
                          alt={item.descricao}
                          className="w-16 h-16 object-cover rounded-lg border border-stone-200 shrink-0"
                        />
                      ) : (
                        <div className="w-16 h-16 rounded-lg bg-stone-200 flex items-center justify-center text-stone-400 shrink-0">
                          <Shirt size={24} />
                        </div>
                      )}
                      <div>
                        <span className="text-xs font-bold uppercase text-emerald-700">
                          {item.categoria_nome} • #PECA-{item.peca_id}
                        </span>
                        <h4 className="font-bold text-stone-800 text-sm">
                          {item.descricao || `Peça #${item.peca_id}`}
                        </h4>
                        <p className="text-xs text-stone-500 mt-0.5">
                          {item.tamanho ? `Tam: ${item.tamanho}` : 'Tam: Único'}
                          {item.marca ? ` • Marca: ${item.marca}` : ''}
                          {item.estado ? ` • Estado: ${item.estado}` : ''}
                        </p>
                      </div>
                    </div>

                    <div className="text-right shrink-0">
                      <span className="text-xs text-stone-400 block">Valor da peça</span>
                      <strong className="text-base font-black text-stone-800">
                        {formatarMoeda(item.preco_venda)}
                      </strong>
                    </div>
                  </div>
                ))
              )}
            </div>

            <div className="bg-stone-50 border-t border-stone-200 px-6 py-4 flex items-center justify-between">
              <span className="text-sm text-stone-600">
                Valor a pagar:{' '}
                <strong className="text-emerald-700 text-base">
                  {formatarMoeda(pedidoDetalhe.valor_total)}
                </strong>
              </span>
              <button
                type="button"
                onClick={() => setPedidoDetalhe(null)}
                className="px-4 py-2 bg-stone-800 hover:bg-stone-900 text-white text-xs font-bold rounded-lg transition"
              >
                Fechar
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}