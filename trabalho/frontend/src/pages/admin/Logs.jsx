import { useState, useEffect } from 'react';
import { ScrollText, Search, Filter, User, Shield, RefreshCw } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

export default function Logs() {
  const [logs, setLogs] = useState([]);
  const [carregando, setCarregando] = useState(true);
  const [busca, setBusca] = useState('');
  const [filtroAcao, setFiltroAcao] = useState('');

  const carregarLogs = async () => {
    setCarregando(true);
    try {
      const resposta = await fetch(`${API_URL}?acao=listar_logs`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      setLogs(Array.isArray(dados) ? dados : []);
    } catch (error) {
      console.error('Erro ao carregar logs:', error);
    } finally {
      setCarregando(false);
    }
  };

  useEffect(() => {
    carregarLogs();
  }, []);

  const acoesDisponiveis = [...new Set(logs.map((l) => l.acao))].sort();

  const logsFiltrados = logs.filter((log) => {
    const termo = busca.toLowerCase();
    const bateTexto =
      (log.descricao || '').toLowerCase().includes(termo) ||
      (log.usuario_nome || '').toLowerCase().includes(termo) ||
      (log.acao || '').toLowerCase().includes(termo);
    const bateAcao = filtroAcao === '' || log.acao === filtroAcao;
    return bateTexto && bateAcao;
  });

  const formatarData = (dataStr) => {
    if (!dataStr) return '-';
    return new Date(dataStr).toLocaleString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });
  };

  const badgeAcao = (acao) => {
    const a = (acao || '').toUpperCase();
    if (a.includes('CANCEL') || a.includes('EXCLU') || a.includes('RECUS')) {
      return 'bg-red-100 text-red-800 border-red-200';
    }
    if (a.includes('APROV') || a.includes('ACEIT') || a.includes('COMPRA')) {
      return 'bg-emerald-100 text-emerald-800 border-emerald-200';
    }
    if (a.includes('EMAIL') || a.includes('PROPOSTA')) {
      return 'bg-purple-100 text-purple-800 border-purple-200';
    }
    if (a.includes('LOGIN') || a.includes('CADASTRO') || a.includes('SENHA')) {
      return 'bg-sky-100 text-sky-800 border-sky-200';
    }
    return 'bg-stone-100 text-stone-700 border-stone-200';
  };

  return (
    <div className="space-y-6 pb-8">
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-stone-200 pb-5">
        <div>
          <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
            <ScrollText className="text-emerald-600" size={26} />
            <span>Logs & Auditoria do Sistema</span>
          </h2>
          <p className="text-stone-500 text-sm mt-1">
            Histórico cronológico de autenticação, lotes, propostas, vendas e envios de e-mail.
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-3">
          <div className="relative">
            <Search className="absolute left-3 top-2.5 text-stone-400" size={17} />
            <input
              type="text"
              placeholder="Buscar usuário ou detalhe..."
              value={busca}
              onChange={(e) => setBusca(e.target.value)}
              className="pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-lg text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 w-56 shadow-xs"
            />
          </div>

          <div className="relative">
            <Filter className="absolute left-3 top-2.5 text-stone-400" size={16} />
            <select
              value={filtroAcao}
              onChange={(e) => setFiltroAcao(e.target.value)}
              className="pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-lg text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-xs"
            >
              <option value="">Todas as ações</option>
              {acoesDisponiveis.map((ac) => (
                <option key={ac} value={ac}>
                  {ac}
                </option>
              ))}
            </select>
          </div>

          <button
            type="button"
            onClick={carregarLogs}
            title="Atualizar logs"
            className="p-2 bg-white border border-stone-300 hover:bg-stone-100 text-stone-700 rounded-lg transition shadow-xs"
          >
            <RefreshCw size={18} />
          </button>
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
                  <th className="p-4 w-44">Data / Hora</th>
                  <th className="p-4 w-52">Usuário</th>
                  <th className="p-4 w-44">Ação</th>
                  <th className="p-4">Descrição Detalhada</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {logsFiltrados.length === 0 ? (
                  <tr>
                    <td colSpan={4} className="p-8 text-center text-stone-500">
                      Nenhum registro de log encontrado.
                    </td>
                  </tr>
                ) : (
                  logsFiltrados.map((log) => (
                    <tr key={log.id} className="hover:bg-stone-50 transition-colors">
                      <td className="p-4 text-xs font-mono text-stone-600 whitespace-nowrap">
                        {formatarData(log.criado_em)}
                      </td>
                      <td className="p-4">
                        <div className="flex items-center gap-2">
                          {log.usuario_tipo === 'admin' ? (
                            <Shield size={15} className="text-emerald-600 shrink-0" />
                          ) : (
                            <User size={15} className="text-stone-400 shrink-0" />
                          )}
                          <div>
                            <strong className="text-stone-800 block text-xs">
                              {log.usuario_nome}
                            </strong>
                            {log.usuario_email && (
                              <span className="text-[11px] text-stone-400">
                                {log.usuario_email}
                              </span>
                            )}
                          </div>
                        </div>
                      </td>
                      <td className="p-4">
                        <span
                          className={`px-2.5 py-1 rounded-full text-xs font-bold border inline-block ${badgeAcao(
                            log.acao
                          )}`}
                        >
                          {log.acao}
                        </span>
                      </td>
                      <td className="p-4 text-stone-700">{log.descricao}</td>
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