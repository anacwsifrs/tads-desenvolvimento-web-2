import { useState, useEffect } from 'react';
import { ShoppingBag, Check, X, FileText } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function GerenciarPedidos() {
  const [pedidos, setPedidos] = useState([]);
  const [carregando, setCarregando] = useState(true);

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

  const handleAprovar = async (id) => {
    if (!window.confirm('Confirmar pagamento PIX deste pedido?')) return;
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
          Confira os pedidos realizados pelos clientes, analise os comprovantes PIX e aprove as baixas.
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
                  pedidos.map((p) => (
                    <tr key={p.id} className="hover:bg-stone-50 transition-colors">
                      <td className="p-4 font-bold text-stone-800">#PED-{p.id}</td>
                      <td className="p-4 font-semibold text-stone-700">{p.cliente_nome}</td>
                      <td className="p-4 text-stone-600">{formatarData(p.criado_em)}</td>
                      <td className="p-4 font-bold text-stone-800">
                        {Number(p.valor_total || 0).toLocaleString('pt-BR', {
                          style: 'currency',
                          currency: 'BRL'
                        })}
                      </td>
                      <td className="p-4">
                        {p.comprovante ? (
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
                          {p.status}
                        </span>
                      </td>
                      <td className="p-4">
                        {p.status === 'Aguardando PIX' ? (
                          <div className="flex items-center justify-center gap-2">
                            <button
                              type="button"
                              onClick={() => handleAprovar(p.id)}
                              className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition"
                            >
                              <Check size={14} />
                              <span>Aprovar</span>
                            </button>
                            <button
                              type="button"
                              onClick={() => handleCancelar(p.id)}
                              className="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition"
                            >
                              <X size={14} />
                              <span>Cancelar</span>
                            </button>
                          </div>
                        ) : (
                          <div className="text-center text-xs text-stone-400 font-medium">
                            Finalizado
                          </div>
                        )}
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}