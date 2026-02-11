import Banner from "../composants/Banner/Banner.tsx";
import ArticleLeft from "../composants/ArticleLeft/ArticleLeft.tsx";
import ArticleRight from "../composants/ArticleRight/ArticleRight.tsx";
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
