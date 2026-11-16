import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Package, Eye, Sparkles } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

const STATUS_MAP = {
  1: { label: 'Aguardando Triagem', badge: 'bg-stone-500 text-white' },
  2: { label: 'Em Avaliação', badge: 'bg-sky-600 text-white' },
  3: { label: 'Proposta Pronta!', badge: 'bg-amber-400 text-stone-900' },
  4: { label: 'Aceito (PIX)', badge: 'bg-emerald-600 text-white' },
  5: { label: 'Aceito (Crédito)', badge: 'bg-teal-500 text-white' },
  6: { label: 'Recusado', badge: 'bg-red-600 text-white' },
  7: { label: 'Doado', badge: 'bg-stone-800 text-white' }
};

export default function MeusLotes() {
  const [lotes, setLotes] = useState([]);
  const [carregando, setCarregando] = useState(true);

  useEffect(() => {
    const carregar = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=meus_lotes`, {
          credentials: 'include'
        });
        const dados = await resposta.json();
        setLotes(Array.isArray(dados) ? dados : []);
      } catch (error) {
        console.error('Erro ao carregar meus lotes:', error);
      } finally {
        setCarregando(false);
      }
    };
    carregar();
  }, []);

  return (
    <div className="space-y-6 pb-8">
      <div className="border-b border-stone-200 pb-5">
        <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
          <Package className="text-emerald-600" size={26} />
          <span>Meus Lotes Cadastrados</span>
        </h2>
        <p className="text-stone-500 text-sm mt-1">
          Acompanhe o status e as propostas dos seus lotes de roupas enviados ao brechó.
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
                  <th className="p-4">#ID</th>
                  <th className="p-4">Descrição</th>
                  <th className="p-4">Status</th>
                  <th className="p-4">Valor Ofertado</th>
                  <th className="p-4 text-center">Ação</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {lotes.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="p-8 text-center text-stone-500">
                      Você ainda não possui nenhum lote cadastrado.
                    </td>
                  </tr>
                ) : (
                  lotes.map((l) => {
                    const statusNum = Number(l.status);
                    const info = STATUS_MAP[statusNum] || {
                      label: 'Em análise',
                      badge: 'bg-stone-400 text-white'
                    };

                    return (
                      <tr key={l.id} className="hover:bg-stone-50 transition-colors">
                        <td className="p-4 font-bold text-stone-800">#{l.id}</td>
                        <td className="p-4 text-stone-700">{l.descricao}</td>
                        <td className="p-4">
                          <span
                            className={`px-3 py-1 rounded-full text-xs font-bold inline-block ${info.badge}`}
                          >
                            {info.label}
                          </span>
                        </td>
                        <td className="p-4 font-bold text-stone-800">
                          {l.valor_proposta
                            ? Number(l.valor_proposta).toLocaleString('pt-BR', {
                                style: 'currency',
                                currency: 'BRL'
                              })
                            : '-'}
                        </td>
                        <td className="p-4 text-center">
                          {statusNum === 3 ? (
                            <Link
                              to={`/proposta/${l.id}`}
                              className="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition"
                            >
                              <Sparkles size={14} />
                              <span>Ver Proposta</span>
                            </Link>
                          ) : (
                            <Link
                              to={`/proposta/${l.id}`}
                              className="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold transition"
                            >
                              <Eye size={14} />
                              <span>Detalhes</span>
                            </Link>
                          )}
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