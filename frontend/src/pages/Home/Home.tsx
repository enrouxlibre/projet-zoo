import Banner from "../../components/Banner/Banner.tsx";
import Article from "../../components/Article/Article.tsx";
import backgroundImg from "../../assets/banner.webp";
import trexImg from "../../assets/trex.jpg";

function Home() {
  return (
    <>
      <Banner
        title="ZOORASSIC"
        catchLine="Entrez dans le royaume des titans"
        backgroundImg={backgroundImg}
      />
      <div className="article-container">
        <Article img={trexImg} title="Le T-Rex, roi du Jurassique">
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas,
          voluptate.
        </Article>
        <Article
          img={trexImg}
          title="Le T-Rex, roi du Jurassique"
          align="right"
        >
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas,
          voluptate.
        </Article>
        <Article img={trexImg} title="Le T-Rex, roi du Jurassique">
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas,
          voluptate.
        </Article>
        <Article
          img={trexImg}
          title="Le T-Rex, roi du Jurassique"
          align="right"
        >
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas,
          voluptate.
        </Article>
      </div>
    </>
  );
}

export default Home;
