import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import {
  Package,
  Plus,
  Scale,
  Pencil,
  Trash2,
  Eye,
  FileText,
  Upload,
  Search
} from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

const STATUS_MAP = {
  1: { label: 'Aguardando Triagem', badge: 'bg-stone-500 text-white' },
  2: { label: 'Em Avaliação', badge: 'bg-sky-600 text-white' },
  3: { label: 'Aguardando Resposta', badge: 'bg-amber-400 text-stone-900' },
  4: { label: 'Aceito (PIX)', badge: 'bg-emerald-600 text-white' },
  5: { label: 'Aceito (Crédito)', badge: 'bg-teal-500 text-white' },
  6: { label: 'Recusado', badge: 'bg-red-600 text-white' },
  7: { label: 'Doado', badge: 'bg-stone-800 text-white' }
};

export default function ListarLotes() {
  const [lotes, setLotes] = useState([]);
  const [busca, setBusca] = useState('');
  const [carregando, setCarregando] = useState(true);
  const [arquivosComprovante, setArquivosComprovante] = useState({});
  const [enviandoId, setEnviandoId] = useState(null);

  const carregarLotes = async () => {
    try {
      const resposta = await fetch(`${API_URL}?acao=listar`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      setLotes(Array.isArray(dados) ? dados : []);
    } catch (error) {
      console.error('Erro ao carregar lotes:', error);
    } finally {
      setCarregando(false);
    }
  };

  useEffect(() => {
    carregarLotes();
  }, []);

  const handleExcluir = async (id) => {
    if (!window.confirm('Tem certeza que deseja excluir este lote?')) return;
    try {
      const resposta = await fetch(`${API_URL}?acao=excluir&id=${id}`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        setLotes((prev) => prev.filter((l) => l.id !== id));
      } else {
        alert(dados.mensagem || 'Erro ao excluir lote.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    }
  };

  const handleUploadComprovante = async (e, loteId) => {
    e.preventDefault();
    const arquivo = arquivosComprovante[loteId];
    if (!arquivo) return;

    setEnviandoId(loteId);
    const formData = new FormData();
    formData.append('lote_id', loteId);
    formData.append('comprovante', arquivo);

    try {
      const resposta = await fetch(`${API_URL}?acao=upload_comprovante`, {
        method: 'POST',
        credentials: 'include',
        body: formData
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        alert(dados.mensagem || 'Comprovante enviado com sucesso!');
        carregarLotes();
      } else {
        alert(dados.mensagem || 'Erro ao enviar comprovante.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    } finally {
      setEnviandoId(null);
    }
  };

  const lotesFiltrados = lotes.filter((l) => {
    const termo = busca.toLowerCase();
    return (
      String(l.id).includes(termo) ||
      (l.cliente_nome || '').toLowerCase().includes(termo) ||
      (l.descricao || '').toLowerCase().includes(termo)
    );
  });

  return (
    <div className="space-y-6 pb-8">
      <div className="bg-white p-6 rounded-xl border border-stone-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
            <Package className="text-emerald-600" size={26} />
            <span>Painel Geral - Lotes de Avaliação</span>
          </h2>
          <p className="text-stone-500 text-sm mt-1">
            Acompanhe triagens, envie propostas e anexe comprovantes de pagamento.
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-3">
          <div className="relative">
            <Search className="absolute left-3 top-2.5 text-stone-400" size={18} />
            <input
              type="text"
              placeholder="Buscar lote ou cliente..."
              value={busca}
              onChange={(e) => setBusca(e.target.value)}
              className="pl-9 pr-4 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>

          <Link
            to="/admin/cadastrar-lote"
            className="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-1.5 shadow-sm transition"
          >
            <Plus size={18} />
            <span>Registrar Novo Lote</span>
          </Link>
        </div>
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
                  <th className="p-4">#ID</th>
                  <th className="p-4">Cliente</th>
                  <th className="p-4">Telefone</th>
                  <th className="p-4">Descrição do Lote</th>
                  <th className="p-4">Status</th>
                  <th className="p-4">Proposta</th>
                  <th className="p-4">Comprovante PIX</th>
                  <th className="p-4 text-center">Ações</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {lotesFiltrados.length === 0 ? (
                  <tr>
                    <td colSpan={8} className="p-8 text-center text-stone-500">
                      Nenhum lote cadastrado.
                    </td>
                  </tr>
                ) : (
                  lotesFiltrados.map((l) => {
                    const statusNum = Number(l.status);
                    const infoStatus = STATUS_MAP[statusNum] || {
                      label: 'Desconhecido',
                      badge: 'bg-stone-400 text-white'
                    };

                    return (
                      <tr key={l.id} className="hover:bg-stone-50 transition-colors">
                        <td className="p-4 font-bold text-stone-800">#{l.id}</td>
                        <td className="p-4 font-semibold text-stone-700">{l.cliente_nome}</td>
                        <td className="p-4 text-stone-600">{l.telefone}</td>
                        <td className="p-4 text-stone-700">{l.descricao}</td>
                        <td className="p-4">
                          <span
                            className={`px-3 py-1 rounded-full text-xs font-bold inline-block ${infoStatus.badge}`}
                          >
                            {infoStatus.label}
                          </span>
                        </td>
                        <td className="p-4 font-bold text-stone-800">
                          {l.valor_proposta !== null
                            ? Number(l.valor_proposta).toLocaleString('pt-BR', {
                                style: 'currency',
                                currency: 'BRL'
                              })
                            : '-'}
                        </td>
                        <td className="p-4">
                          {statusNum === 4 ? (
                            l.comprovante ? (
                              <a
                                href={`${UPLOADS_URL}${l.comprovante}`}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-800 font-bold underline text-xs"
                              >
                                <FileText size={15} />
                                <span>Ver Anexo</span>
                              </a>
                            ) : (
                              <form
                                onSubmit={(e) => handleUploadComprovante(e, l.id)}
                                className="flex items-center gap-1.5"
                              >
                                <input
                                  type="file"
                                  accept="image/*,.pdf"
                                  required
                                  onChange={(e) =>
                                    setArquivosComprovante((prev) => ({
                                      ...prev,
                                      [l.id]: e.target.files[0]
                                    }))
                                  }
                                  className="text-xs w-36 text-stone-600 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200"
                                />
                                <button
                                  type="submit"
                                  disabled={enviandoId === l.id}
                                  className="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded text-xs font-bold flex items-center gap-1 transition disabled:opacity-60"
                                >
                                  <Upload size={13} />
                                  <span>{enviandoId === l.id ? '...' : 'Enviar'}</span>
                                </button>
                              </form>
                            )
                          ) : (
                            <span className="text-xs text-stone-400">N/A</span>
                          )}
                        </td>
                        <td className="p-4">
                          <div className="flex items-center justify-center gap-1.5">
                            {statusNum < 3 ? (
                              <>
                                <Link
                                  to={`/admin/avaliar-lote/${l.id}`}
                                  title="Avaliar Lote"
                                  className="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition"
                                >
                                  <Scale size={14} />
                                  <span>Avaliar</span>
                                </Link>
                                <Link
                                  to={`/admin/editar-lote/${l.id}`}
                                  title="Editar Lote"
                                  className="p-1.5 bg-amber-400 hover:bg-amber-500 text-stone-900 rounded-lg transition"
                                >
                                  <Pencil size={15} />
                                </Link>
                                <button
                                  type="button"
                                  onClick={() => handleExcluir(l.id)}
                                  title="Excluir Lote"
                                  className="p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition"
                                >
                                  <Trash2 size={15} />
                                </button>
                              </>
                            ) : (
                              <Link
                                to={`/admin/avaliar-lote/${l.id}`}
                                title="Ver Detalhes da Proposta"
                                className="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold flex items-center gap-1.5 transition"
                              >
                                <Eye size={14} />
                                <span>Ver Detalhes</span>
                              </Link>
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
    </div>
  );
}