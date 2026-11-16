import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Users, Eye, Pencil, Search } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

export default function Clientes() {
  const [clientes, setClientes] = useState([]);
  const [busca, setBusca] = useState('');
  const [carregando, setCarregando] = useState(true);

  useEffect(() => {
    const carregarClientes = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=clientes`, {
          credentials: 'include'
        });
        const dados = await resposta.json();
        setClientes(Array.isArray(dados) ? dados : []);
      } catch (error) {
        console.error('Erro ao carregar clientes:', error);
      } finally {
        setCarregando(false);
      }
    };
    carregarClientes();
  }, []);

  const clientesFiltrados = clientes.filter((c) => {
    const termo = busca.toLowerCase();
    return (
      (c.nome || '').toLowerCase().includes(termo) ||
      (c.cpf || '').toLowerCase().includes(termo) ||
      (c.telefone || '').toLowerCase().includes(termo)
    );
  });

  const formatarMoeda = (valor) =>
    Number(valor || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

  return (
    <div className="space-y-6 pb-8">
      <div className="bg-white p-6 rounded-xl border border-stone-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
            <Users className="text-emerald-600" size={26} />
            <span>Painel de Usuários</span>
          </h2>
          <p className="text-stone-500 text-sm mt-1">
            Gerencie clientes cadastrados e saldos de crédito na loja.
          </p>
        </div>

        <div className="relative">
          <Search className="absolute left-3 top-2.5 text-stone-400" size={18} />
          <input
            type="text"
            placeholder="Buscar por nome, CPF..."
            value={busca}
            onChange={(e) => setBusca(e.target.value)}
            className="pl-9 pr-4 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 w-full sm:w-64"
          />
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
                  <th className="p-4">CPF</th>
                  <th className="p-4">Nome</th>
                  <th className="p-4">WhatsApp</th>
                  <th className="p-4">Saldo (Créditos)</th>
                  <th className="p-4 w-32 text-center">Ações</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {clientesFiltrados.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="p-8 text-center text-stone-500">
                      Nenhum cliente cadastrado.
                    </td>
                  </tr>
                ) : (
                  clientesFiltrados.map((c) => (
                    <tr key={c.id} className="hover:bg-stone-50 transition-colors">
                      <td className="p-4 font-mono text-stone-600">{c.cpf}</td>
                      <td className="p-4 font-bold text-stone-800">{c.nome}</td>
                      <td className="p-4 text-stone-600">{c.telefone}</td>
                      <td className="p-4 font-bold text-emerald-600">
                        {formatarMoeda(c.saldo_credito)}
                      </td>
                      <td className="p-4">
                        <div className="flex items-center justify-center gap-2">
                          <Link
                            to={`/admin/clientes/ver/${c.id}`}
                            title="Visualizar"
                            className="p-2 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white border border-sky-200 rounded-lg transition"
                          >
                            <Eye size={16} />
                          </Link>
                          <Link
                            to={`/admin/clientes/editar/${c.id}`}
                            title="Editar"
                            className="p-2 bg-amber-50 hover:bg-amber-500 text-amber-700 hover:text-white border border-amber-200 rounded-lg transition"
                          >
                            <Pencil size={16} />
                          </Link>
                        </div>
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