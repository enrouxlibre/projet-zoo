import Banner from "../Banner/Banner";
import ArticleLeft from "../ArticleLeft/ArticleLeft";
import ArticleRight from "../ArticleRight/ArticleRight";
import backgroundImg from "../assets/banner.webp";
import trexImg from "../assets/trex.jpg";

function Accueil() {
  return (
    <>
      <Banner
        title="JURASSICZOO"
        catchLine="Entrez dans le royaume des titans"
        backgroundImg={backgroundImg}
      />
      <ArticleLeft img={trexImg} title="Le T-Rex, roi du Jurassique">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas,
        voluptate.
      </ArticleLeft>
      <ArticleRight img={trexImg} title="Le T-Rex, roi du Jurassique">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas,
        voluptate.
      </ArticleRight>
      <ArticleLeft img={trexImg} title="Le T-Rex, roi du Jurassique">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas,
        voluptate.
      </ArticleLeft>
      <ArticleRight img={trexImg} title="Le T-Rex, roi du Jurassique">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas,
        voluptate.
      </ArticleRight>
    </>
  );
}

export default Accueil;
