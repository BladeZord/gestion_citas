import React from 'react';
import { BrowserRouter, Route, Routes } from "react-router-dom";
import { Layout } from "antd";
import "antd/dist/reset.css";
import { Login } from "./modules/autenticacion";
import { ListadoTiposCita, ListadoCitas, CrearCita, EditarCita, ListadoUsuarios, Dashboard } from "./modules/mantenimiento";
import Navegacion from "./components/Plantilla/Navegacion";

const { Content } = Layout;

function App(): React.ReactElement {
  return (
    <BrowserRouter>
      <Routes>
        {/* Ruta inicial - Login */}
        <Route path="/" element={<Login />} />
        <Route path="/login" element={<Login />} />
        
        {/* Rutas protegidas con layout */}
        <Route path="/dashboard" element={
          <Layout style={{ minHeight: "100vh" }}>
            <Navegacion/>
            <Layout>
              <Content style={{ padding: "24px", background: "#fff" }}>
                <Dashboard/>
              </Content>
            </Layout>
          </Layout>
        } />
        
        {/* Rutas de Mantenimiento - Tipos de Cita */}
        <Route path="/tipos-cita" element={
          <Layout style={{ minHeight: "100vh" }}>
            <Navegacion/>
            <Layout>
              <Content style={{ padding: "24px", background: "#fff" }}>
                <ListadoTiposCita/>
              </Content>
            </Layout>
          </Layout>
        } />
        
        {/* Rutas de Mantenimiento - Citas */}
        <Route path="/citas" element={
          <Layout style={{ minHeight: "100vh" }}>
            <Navegacion/>
            <Layout>
              <Content style={{ padding: "24px", background: "#fff" }}>
                <ListadoCitas/>
              </Content>
            </Layout>
          </Layout>
        } />
        <Route path="/citas/crear" element={
          <Layout style={{ minHeight: "100vh" }}>
            <Navegacion/>
            <Layout>
              <Content style={{ padding: "24px", background: "#fff" }}>
                <CrearCita/>
              </Content>
            </Layout>
          </Layout>
        } />
        <Route path="/citas/editar/:id" element={
          <Layout style={{ minHeight: "100vh" }}>
            <Navegacion/>
            <Layout>
              <Content style={{ padding: "24px", background: "#fff" }}>
                <EditarCita/>
              </Content>
            </Layout>
          </Layout>
        } />
        
        {/* Rutas de Mantenimiento - Usuarios */}
        <Route path="/usuarios" element={
          <Layout style={{ minHeight: "100vh" }}>
            <Navegacion/>
            <Layout>
              <Content style={{ padding: "24px", background: "#fff" }}>
                <ListadoUsuarios/>
              </Content>
            </Layout>
          </Layout>
        } />
      </Routes>
    </BrowserRouter>
  );
}

export default App;

