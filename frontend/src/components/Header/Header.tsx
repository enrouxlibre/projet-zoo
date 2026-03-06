import { NavLink } from "react-router";
import logo from "../../assets/logo.png";
import "./Header.scss";
import { logout } from "../../api/auth";
import { useEffect, useState } from "react";
function Header() {
  const [token, setToken] = useState<string | null>(null);

  useEffect(() => {
    setToken(localStorage.getItem("csrfToken"));
  }, []);

  console.log(token);
  return (
    <div className="header">
      <img src={logo} alt="logo" className="logo" />
      <nav>
        <ul className="menu">
          <li>
            <NavLink to="/">Accueil</NavLink>
          </li>

          <li>
            <NavLink to="/dinosaurs">Nos dinosaures</NavLink>
          </li>

          <li>
            <NavLink to="/contact">Contact</NavLink>
          </li>

          {!token ? (
            <li>
              <NavLink to="/login">Connexion</NavLink>
            </li>
          ) : (
            <li>
              <NavLink
                to="/"
                className="logout"
                onClick={() => {
                  logout();
                  setToken(null);
                }}
              >
                Déconnexion
              </NavLink>
            </li>
          )}
        </ul>
      </nav>
    </div>
  );
}

export default Header;
