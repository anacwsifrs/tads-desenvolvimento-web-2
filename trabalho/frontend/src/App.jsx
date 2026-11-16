import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout';

// Públicas
import Inicio from './pages/public/Inicio';
import Vitrine from './pages/public/Vitrine';
import Detalhar from './pages/public/Detalhar';
import Login from './pages/public/Login';
import Cadastro from './pages/public/Cadastro';
import RecuperarSenha from './pages/public/RecuperarSenha';
import FaleConosco from './pages/public/FaleConosco';
import Termos from './pages/public/Termos';

// Cliente
import Perfil from './pages/cliente/Perfil';
import Carrinho from './pages/cliente/Carrinho';
import MeusLotes from './pages/cliente/MeusLotes';
import MeusPedidos from './pages/cliente/MeusPedidos';
import Proposta from './pages/cliente/Proposta';

// Admin
import ListarLotes from './pages/admin/ListarLotes';
import CadastrarLote from './pages/admin/CadastrarLote';
import AvaliarLote from './pages/admin/AvaliarLote';
import EditarLote from './pages/admin/EditarLote';
import Clientes from './pages/admin/Clientes';
import FormClienteAdmin from './pages/admin/FormClienteAdmin';
import GerenciarPedidos from './pages/admin/GerenciarPedidos';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Layout />}>
          {/* Públicas */}
          <Route index element={<Inicio />} />
          <Route path="vitrine" element={<Vitrine />} />
          <Route path="detalhar/:id" element={<Detalhar />} />
          <Route path="login" element={<Login />} />
          <Route path="cadastro" element={<Cadastro />} />
          <Route path="recuperar-senha" element={<RecuperarSenha />} />
          <Route path="fale-conosco" element={<FaleConosco />} />
          <Route path="termos" element={<Termos />} />

          {/* Cliente */}
          <Route path="perfil" element={<Perfil />} />
          <Route path="carrinho" element={<Carrinho />} />
          <Route path="meus-lotes" element={<MeusLotes />} />
          <Route path="meus-pedidos" element={<MeusPedidos />} />
          <Route path="proposta/:id" element={<Proposta />} />

          {/* Admin */}
          <Route path="admin/lotes" element={<ListarLotes />} />
          <Route path="admin/cadastrar-lote" element={<CadastrarLote />} />
          <Route path="admin/avaliar-lote/:id" element={<AvaliarLote />} />
          <Route path="admin/editar-lote/:id" element={<EditarLote />} />
          <Route path="admin/clientes" element={<Clientes />} />
          <Route path="admin/clientes/ver/:id" element={<FormClienteAdmin readonly={true} />} />
          <Route path="admin/clientes/editar/:id" element={<FormClienteAdmin readonly={false} />} />
          <Route path="admin/pedidos" element={<GerenciarPedidos />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;