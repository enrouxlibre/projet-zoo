import { NavLink } from "react-router";
import logo from "../../assets/logo.png";
import "./Header.scss";
function Header() {
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

          <li>
            <NavLink to="/login">Connexion</NavLink>
          </li>
        </ul>
      </nav>
    </div>
  );
}

export default Header;
