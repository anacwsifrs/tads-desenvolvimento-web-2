import { useState, useEffect } from 'react';
import {
  ShoppingBag,
  FileText,
  Upload,
  CheckCircle2,
  XCircle,
  Clock,
  Eye,
  X,
  Shirt,
  Wallet
} from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function MeusPedidos() {
  const [pedidos, setPedidos] = useState([]);
  const [carregando, setCarregando] = useState(true);
  const [arquivos, setArquivos] = useState({});
  const [enviandoId, setEnviandoId] = useState(null);
  const [pedidoDetalhe, setPedidoDetalhe] = useState(null);
  const [carregandoDetalhe, setCarregandoDetalhe] = useState(false);

  const carregarPedidos = async () => {
    try {
      const resposta = await fetch(`${API_URL}?acao=meus_pedidos`, {
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
        alert(dados.mensagem || 'Não foi possível carregar os itens do pedido.');
        setPedidoDetalhe(null);
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
      setPedidoDetalhe(null);
    } finally {
      setCarregandoDetalhe(false);
    }
  };

  const handleUpload = async (e, pedidoId) => {
    e.preventDefault();
    const arquivo = arquivos[pedidoId];
    if (!arquivo) return;

    setEnviandoId(pedidoId);
    const formData = new FormData();
    formData.append('pedido_id', pedidoId);
    formData.append('comprovante', arquivo);

    try {
      const resposta = await fetch(`${API_URL}?acao=upload_comprovante_pix`, {
        method: 'POST',
        credentials: 'include',
        body: formData
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        alert(dados.mensagem || 'Comprovante enviado!');
        carregarPedidos();
      } else {
        alert(dados.mensagem || 'Falha ao enviar comprovante.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    } finally {
      setEnviandoId(null);
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

  return (
    <div className="space-y-6 pb-8">
      <div className="border-b border-stone-200 pb-5">
        <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
          <ShoppingBag className="text-emerald-600" size={26} />
          <span>Minhas Compras e Reservas</span>
        </h2>
        <p className="text-stone-500 text-sm mt-1">
          Acompanhe seus pedidos, visualize as peças reservadas e envie o comprovante PIX quando necessário.
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
                  <th className="p-4">Data</th>
                  <th className="p-4">Valor a Pagar</th>
                  <th className="p-4">Status</th>
                  <th className="p-4">Comprovante PIX</th>
                  <th className="p-4 text-center">Peças</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {pedidos.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="p-8 text-center text-stone-500">
                      Você ainda não realizou nenhuma compra.
                    </td>
                  </tr>
                ) : (
                  pedidos.map((p) => {
                    const pagoComSaldoIntegral = Number(p.valor_total) === 0;

                    return (
                      <tr key={p.id} className="hover:bg-stone-50 transition-colors">
                        <td className="p-4 font-bold text-stone-800">#PED-{p.id}</td>
                        <td className="p-4 text-stone-600">{formatarData(p.criado_em)}</td>
                        <td className="p-4 font-bold text-emerald-600">
                          {pagoComSaldoIntegral ? (
                            <span className="inline-flex items-center gap-1 text-teal-700 bg-teal-50 border border-teal-200 px-2.5 py-0.5 rounded-full text-xs">
                              <Wallet size={13} /> R$ 0,00 (Saldo)
                            </span>
                          ) : (
                            formatarMoeda(p.valor_total)
                          )}
                        </td>
                        <td className="p-4">
                          {p.status === 'Aprovado' && (
                            <span className="inline-flex items-center gap-1 text-emerald-700 font-bold text-xs">
                              <CheckCircle2 size={15} /> Aprovado
                            </span>
                          )}
                          {p.status === 'Cancelado' && (
                            <span className="inline-flex items-center gap-1 text-red-600 font-bold text-xs">
                              <XCircle size={15} /> Cancelado
                            </span>
                          )}
                          {p.status !== 'Aprovado' && p.status !== 'Cancelado' && (
                            <span className="inline-flex items-center gap-1 text-amber-600 font-bold text-xs">
                              <Clock size={15} />
                              {pagoComSaldoIntegral ? 'Aguardando Aprovação' : 'Aguardando PIX'}
                            </span>
                          )}
                        </td>
                        <td className="p-4">
                          {pagoComSaldoIntegral ? (
                            <span className="text-xs text-stone-400 italic">Dispensado (Pago com Saldo)</span>
                          ) : p.comprovante ? (
                            <a
                              href={`${UPLOADS_URL}${p.comprovante}`}
                              target="_blank"
                              rel="noreferrer"
                              className="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-800 font-bold underline text-xs"
                            >
                              <FileText size={15} />
                              <span>Ver Comprovante</span>
                            </a>
                          ) : p.status === 'Aguardando PIX' ? (
                            <form
                              onSubmit={(e) => handleUpload(e, p.id)}
                              className="flex items-center gap-2"
                            >
                              <input
                                type="file"
                                accept="image/*,.pdf"
                                required
                                onChange={(e) =>
                                  setArquivos((prev) => ({
                                    ...prev,
                                    [p.id]: e.target.files[0]
                                  }))
                                }
                                className="text-xs w-40 text-stone-600 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-stone-100 file:text-stone-700"
                              />
                              <button
                                type="submit"
                                disabled={enviandoId === p.id}
                                className="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 transition disabled:opacity-60"
                              >
                                <Upload size={13} />
                                <span>{enviandoId === p.id ? '...' : 'Enviar'}</span>
                              </button>
                            </form>
                          ) : (
                            <span className="text-stone-400">-</span>
                          )}
                        </td>
                        <td className="p-4 text-center">
                          <button
                            type="button"
                            onClick={() => abrirDetalhes(p.id)}
                            className="inline-flex items-center gap-1 px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold transition shadow-sm"
                          >
                            <Eye size={14} />
                            <span>Ver Peças</span>
                          </button>
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
                {pedidoDetalhe.criado_em && (
                  <p className="text-xs text-stone-300">
                    Realizado em {formatarData(pedidoDetalhe.criado_em)} • Status: {pedidoDetalhe.status}
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
                          {item.categoria_nome}
                        </span>
                        <h4 className="font-bold text-stone-800 text-sm">
                          {item.descricao || `Peça #${item.peca_id}`}
                        </h4>
                        <p className="text-xs text-stone-500 mt-0.5">
                          {item.tamanho ? `Tam: ${item.tamanho}` : 'Tam: Único'}
                          {item.marca ? ` • Marca: ${item.marca}` : ''}
                          {item.cor ? ` • Cor: ${item.cor}` : ''}
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
                Valor final cobrado:{' '}
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