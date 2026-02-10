import "./App.css";
import Accueil from "./pages/Accueil.tsx";
import Login from "./pages/Login.tsx";
import BackOffice from "./pages/BackOffice.tsx";
import ErrorPage from "./pages/ErrorPage.tsx";
import {
  BrowserRouter as Routes,
  Route,
  BrowserRouter,
} from "react-router-dom";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Accueil />} />
        <Route path="/login" element={<Login />} />
        <Route path="/connected" element={<BackOffice />} />
        <Route path="/*" element={<ErrorPage />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
