import "./App.css";
import { Routes, Route, BrowserRouter } from "react-router";
import Header from "./composants/Header/Header.tsx";
import Accueil from "./pages/Accueil.tsx";
import Login from "./pages/Login.tsx";
import BackOffice from "./pages/BackOffice.tsx";
import ErrorPage from "./pages/ErrorPage.tsx";

function App() {
  return (
    <BrowserRouter>
      <Header />
      <main>
        <Routes>
          <Route path="/" element={<Accueil />} />
          <Route path="/login" element={<Login />} />
          <Route path="/connected" element={<BackOffice />} />
          <Route path="*" element={<ErrorPage />} />
        </Routes>
      </main>
    </BrowserRouter>
  );
}

export default App;
