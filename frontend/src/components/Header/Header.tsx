import logo from "../../assets/logo.png";
import "./Header.scss";
function Header() {
  return (
    <div className="header">
      <img src={logo} alt="logo" className="logo" />
      <nav>
        <ul className="menu">
          <a href="#">
            <li>Accueil</li>
          </a>
          <a href="#">
            <li>Nos dinosaures</li>
          </a>
          <a href="#">
            <li>Contact</li>
          </a>
          <a href="#">
            <li>Connexion</li>
          </a>
        </ul>
      </nav>
    </div>
  );
}

export default Header;
