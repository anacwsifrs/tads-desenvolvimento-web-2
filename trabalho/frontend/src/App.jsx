import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout';
import Inicio from './pages/public/Inicio';
import Vitrine from './pages/public/Vitrine';
import Detalhar from './pages/public/Detalhar';
import Login from './pages/public/Login';
import Cadastro from './pages/public/Cadastro';
import CadastrarLote from './pages/admin/CadastrarLote';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Layout />}>
          <Route index element={<Inicio />} />
          <Route path="vitrine" element={<Vitrine />} />
          <Route path="detalhar/:id" element={<Detalhar />} />
          <Route path="login" element={<Login />} />
          <Route path="cadastro" element={<Cadastro />} />
          <Route path="admin/cadastrar-lote" element={<CadastrarLote />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;