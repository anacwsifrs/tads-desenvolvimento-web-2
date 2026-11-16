import { useState, useEffect } from 'react';
import { ShoppingBag, FileText, Upload, CheckCircle2, XCircle, Clock } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function MeusPedidos() {
  const [pedidos, setPedidos] = useState([]);
  const [carregando, setCarregando] = useState(true);
  const [arquivos, setArquivos] = useState({});
  const [enviandoId, setEnviandoId] = useState(null);

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

  return (
    <div className="space-y-6 pb-8">
      <div className="bg-white p-6 rounded-xl border border-stone-200 shadow-sm">
        <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
          <ShoppingBag className="text-emerald-600" size={26} />
          <span>Minhas Compras e Reservas</span>
        </h2>
        <p className="text-stone-500 text-sm mt-1">
          Acompanhe seus pedidos e envie o comprovante PIX para confirmação da reserva.
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
                  <th className="p-4">Valor Total</th>
                  <th className="p-4">Status</th>
                  <th className="p-4">Comprovante PIX</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {pedidos.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="p-8 text-center text-stone-500">
                      Você ainda não realizou nenhuma compra.
                    </td>
                  </tr>
                ) : (
                  pedidos.map((p) => (
                    <tr key={p.id} className="hover:bg-stone-50 transition-colors">
                      <td className="p-4 font-bold text-stone-800">#PED-{p.id}</td>
                      <td className="p-4 text-stone-600">{formatarData(p.criado_em)}</td>
                      <td className="p-4 font-bold text-emerald-600">
                        {Number(p.valor_total || 0).toLocaleString('pt-BR', {
                          style: 'currency',
                          currency: 'BRL'
                        })}
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
                            <Clock size={15} /> Aguardando PIX
                          </span>
                        )}
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