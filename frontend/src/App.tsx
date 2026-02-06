import "./App.css";
import { BrowserRouter as Router, Routes, Route, BrowserRouter } from "react-router-dom";    
import logo from "./assets/logo.png";

function App() {
  return (
    <BrowserRouter>
    <Routes>
      <Route path="/" element={<Accueil />} />
      <Route path="/login" element={<Login />} />
      <Route path="/" element={}>
      <Route path="/" element={}>
      </Routes>
    </BrowserRouter>

  );
}

export default App;
