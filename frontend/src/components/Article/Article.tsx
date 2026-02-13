import SlideInSection from "../SlideInSection/SlideInSection";
import "./Article.scss";

function Article({
  img,
  title,
  align = "left",
  children,
}: {
  img: string;
  title: string;
  align?: "left" | "right";
  children: React.ReactNode;
}) {
  return (
    <SlideInSection align={align}>
      <div className={`article ${align}`}>
        <img src={img} alt={`image de ${title}`} />
        <div className="article-text">
          <h3>{title}</h3>
          <p>{children}</p>
        </div>
      </div>
    </SlideInSection>
  );
}

export default Article;
